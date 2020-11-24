<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Controller\Manage;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Controller\AbstractManage;
use BKozlic\MultipleWishlist\Helper\Data;
use BKozlic\MultipleWishlist\Model\MultipleWishlistFactory;
use BKozlic\MultipleWishlist\Model\MultipleWishlistRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;
use Psr\Log\LoggerInterface;

/**
 * Controller for multiple wishlist creation
 */
class Create extends AbstractManage implements HttpPostActionInterface
{
    /**
     * @var MultipleWishlistFactory
     */
    protected $multipleWishlistFactory;

    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Random
     */
    protected $mathRandom;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * Create constructor.
     *
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param MultipleWishlistFactory $multipleWishlistFactory
     * @param MultipleWishlistRepository $multipleWishlistRepository
     * @param WishlistHelper $wishlistHelper
     * @param LoggerInterface $logger
     * @param Random $mathRandom
     * @param Validator $formKeyValidator
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Data $moduleHelper
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        MultipleWishlistFactory $multipleWishlistFactory,
        MultipleWishlistRepository $multipleWishlistRepository,
        WishlistHelper $wishlistHelper,
        LoggerInterface $logger,
        Random $mathRandom,
        Validator $formKeyValidator,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Data $moduleHelper
    ) {
        parent::__construct($context, $urlBuilder, $formKeyValidator, $multipleWishlistRepository);
        $this->multipleWishlistFactory = $multipleWishlistFactory;
        $this->wishlistHelper = $wishlistHelper;
        $this->logger = $logger;
        $this->mathRandom = $mathRandom;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Process multiple wishlist creation
     *
     * @throws LocalizedException
     * @return Json|Redirect
     */
    public function execute()
    {
        //@TODO Move item to another wishlist functionality
        $params = $this->getRequest()->getParams();
        $wishlistId = $this->wishlistHelper->getWishlist()->getId();

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->processReturn(
                __('Invalid Form Key. Please refresh the page.'),
                false
            );
        }

        if (!$wishlistId || !isset($params['name']) || !trim($params['name'])) {
            return $this->processReturn(
                __('Required data missing.'),
                false
            );
        }

        $wishlistExceeded = $this->checkLimit($wishlistId);
        if ($wishlistExceeded) {
            return $this->processReturn(
                __('You have reached maximum amount of the wishlists.'),
                false
            );
        }

        $create = $this->createWishlist($params, $wishlistId);
        if (!$create) {
            return $this->processReturn(
                __('Something went wrong while saving the wishlist.'),
                false
            );
        }

        return $this->processReturn(
            __('Wishlist has been successfully saved.')
        );
    }

    /**
     * Checks if multiple wishlist limit number is exceeded
     *
     * @param int $wishlistId
     * @return bool
     */
    protected function checkLimit($wishlistId)
    {
        $this->searchCriteriaBuilder->addFilter(
            MultipleWishlistInterface::WISHLIST_ID,
            $wishlistId
        );

        $limit = $this->moduleHelper->getWishlistLimit();
        $multipleWishlists = $this->multipleWishlistRepository->getList($this->searchCriteriaBuilder->create())
            ->getItems();

        if ($limit <= count($multipleWishlists)) {
            return true;
        }

        return false;
    }

    /**
     * Creates a multiple wishlist
     *
     * @param array $params
     * @param int $wishlistId
     * @throws LocalizedException
     * @return bool
     */
    protected function createWishlist(array $params, int $wishlistId)
    {
        $multipleWishlist = $this->multipleWishlistFactory->create();
        $multipleWishlist->setWishlistId($wishlistId);
        $multipleWishlist->setName($params['name']);
        $multipleWishlist->setSharingCode($this->mathRandom->getUniqueHash());

        try {
            $this->multipleWishlistRepository->save($multipleWishlist);
        } catch (CouldNotSaveException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }

        return true;
    }
}
