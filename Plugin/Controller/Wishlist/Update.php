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
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Message\MessageInterface;
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
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * Update Plugin constructor.
     * @param MultipleWishlistItemRepositoryInterface $itemRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Data $moduleHelper
     * @param LoggerInterface $logger
     * @param LocaleQuantityProcessor $quantityProcessor
     * @param ResultFactory $resultFactory
     * @param WishlistProviderInterface $wishlistProvider
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        MultipleWishlistItemRepositoryInterface $itemRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Data $moduleHelper,
        LoggerInterface $logger,
        LocaleQuantityProcessor $quantityProcessor,
        ResultFactory $resultFactory,
        WishlistProviderInterface $wishlistProvider,
        ManagerInterface $messageManager
    ) {
        $this->itemRepository = $itemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->moduleHelper = $moduleHelper;
        $this->logger = $logger;
        $this->quantityProcessor = $quantityProcessor;
        $this->resultFactory = $resultFactory;
        $this->wishlistProvider = $wishlistProvider;
        $this->messageManager = $messageManager;
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
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $mainExecute = $proceed();

        if (!$this->moduleHelper->isEnabled()) {
            return $mainExecute;
        }

        $errorMessages = $this->messageManager->getMessages()->getItemsByType(MessageInterface::TYPE_ERROR);

        if (count($errorMessages)) {
            return $mainExecute;
        }

        $request = $subject->getRequest();
        $wishlist = $this->wishlistProvider->getWishlist();
        $multipleWishlist = $request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        if ($multipleWishlist === null) {
            return $mainExecute;
        }

        if ($request->getParam('save_and_share') !== null) {
            $resultRedirect->setPath('*/*/share', [
                'wishlist_id' => $wishlist->getId(),
                MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME => $multipleWishlist
            ]);
            return $resultRedirect;
        }

        $resultRedirect->setPath('*', [
            'wishlist_id' => $wishlist->getId(),
            MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME => $multipleWishlist
        ]);
        return $resultRedirect;
    }
}
