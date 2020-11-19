<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Plugin\Controller\Wishlist;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Api\MultipleWishlistItemRepositoryInterface;
use BKozlic\MultipleWishlist\Helper\Data;
use Closure;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Wishlist\Controller\Index\Update as WishlistUpdate;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Model\LocaleQuantityProcessor;
use Psr\Log\LoggerInterface;

/**
 * Plugin class for updating multiple wishlist items
 */
class Update
{
    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var MultipleWishlistItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var LocaleQuantityProcessor
     */
    protected $quantityProcessor;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * Update Plugin constructor.
     * @param Validator $formKeyValidator
     * @param MultipleWishlistItemRepositoryInterface $itemRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Data $moduleHelper
     * @param LoggerInterface $logger
     * @param LocaleQuantityProcessor $quantityProcessor
     * @param ResultFactory $resultFactory
     * @param WishlistProviderInterface $wishlistProvider
     */
    public function __construct(
        Validator $formKeyValidator,
        MultipleWishlistItemRepositoryInterface $itemRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Data $moduleHelper,
        LoggerInterface $logger,
        LocaleQuantityProcessor $quantityProcessor,
        ResultFactory $resultFactory,
        WishlistProviderInterface $wishlistProvider
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->itemRepository = $itemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->moduleHelper = $moduleHelper;
        $this->logger = $logger;
        $this->quantityProcessor = $quantityProcessor;
        $this->resultFactory = $resultFactory;
        $this->wishlistProvider = $wishlistProvider;
    }

    /**
     * Makes sure that all qtys in request are valid
     *
     * @param WishlistUpdate $subject
     * @param RequestInterface $request
     * @return array
     */
    public function beforeDispatch(WishlistUpdate $subject, RequestInterface $request)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return [$request];
        }

        $qtys = $request->getParam('qty');
        $processedQtys = [];
        if (is_array($qtys)) {
            foreach ($qtys as $key => $qty) {
                $processedQty = $this->quantityProcessor->process($qty);
                if (!$processedQty) {
                    $processedQty = 1;
                }

                $processedQtys[$key] = $processedQty;
            }
        }

        $params = $request->getParams();
        $params['qty'] = $processedQtys;
        $request->setParams($params);

        return [$request];
    }

    /**
     * Process updating multiple wishlist items
     *
     * @param WishlistUpdate $subject
     * @param Closure $proceed
     * @return mixed
     */
    public function aroundExecute(WishlistUpdate $subject, Closure $proceed)
    {
        $mainExecute = $proceed();
        //@TODO Redirect to selected wishlist.
        //@TODO Clear all messages and add messages in the plugin(s).
        //@TODO After configure product description is removed.
        //@TODO Wishlist shared/index contains wrong items.
        if (!$this->moduleHelper->isEnabled()) {
            return $mainExecute;
        }

        $request = $subject->getRequest();
        if (!$this->formKeyValidator->validate($request)) {
            return $mainExecute;
        }

        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            return $mainExecute;
        }

        $multipleWishlist = $request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        if ($multipleWishlist === null) {
            return $mainExecute;
        }

        $itemList = $this->moduleHelper->getMultipleWishlistItems($multipleWishlist);
        $descriptions = $request->getParam('description');
        $qtys = $request->getParam('qty');

        foreach ($itemList as $item) {
            if (isset($descriptions[$item->getWishlistItemId()]) &&
                $descriptions[$item->getWishlistItemId()] !== $item->getDescription()) {
                $item->setDescription($descriptions[$item->getWishlistItemId()]);
                $item->setData('changed', true);
            }

            //@TODO Check if there is error message. Check Update controller.
            if (isset($qtys[$item->getWishlistItemId()]) &&
                $qtys[$item->getWishlistItemId()] != $item->getQty()) {
                $qty = $this->quantityProcessor->process($qtys[$item->getWishlistItemId()]);

                if (!$qty) {
                    $qty = 1;
                }

                $item->setQty($qty);
                $item->setData('changed', true);
            }

            if ($item->getData('changed')) {
                try {
                    $this->itemRepository->save($item);
                    $this->moduleHelper->recalculate($item->getWishlistItemId());
                } catch (CouldNotSaveException $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }

        if ($request->getParam('save_and_share') !== null) {
            //@TODO Redirect to the right wishlist after share
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/share', [
                'wishlist_id' => $wishlist->getId(),
                MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME => $multipleWishlist
            ]);
            return $resultRedirect;
        }

        return $mainExecute;
    }
}
