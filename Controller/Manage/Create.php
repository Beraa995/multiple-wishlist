<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Controller\Manage;

use BKozlic\MultipleWishlist\Model\MultipleWishlistFactory;
use BKozlic\MultipleWishlist\Model\MultipleWishlistRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Wishlist\Helper\Data as WishlistHelper;

class Create extends Action implements HttpPostActionInterface
{
    /**
     * @var MultipleWishlistFactory
     */
    protected $multipleWishlistFactory;

    /**
     * @var MultipleWishlistRepository
     */
    protected $multipleWishlistRepository;

    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * Create constructor.
     * @param Context $context
     * @param MultipleWishlistFactory $multipleWishlistFactory
     * @param MultipleWishlistRepository $multipleWishlistRepository
     * @param WishlistHelper $wishlistHelper
     */
    public function __construct(
        Context $context,
        MultipleWishlistFactory $multipleWishlistFactory,
        MultipleWishlistRepository $multipleWishlistRepository,
        WishlistHelper $wishlistHelper
    ) {
        parent::__construct($context);
        $this->multipleWishlistFactory = $multipleWishlistFactory;
        $this->multipleWishlistRepository = $multipleWishlistRepository;
        $this->wishlistHelper = $wishlistHelper;
    }

    /**
     * Process multiple wishlist creation
     */
    public function execute()
    {
        //@TODO Limit number of wishlists with system configuration
        $params = $this->getRequest()->getParams();
        $wishlistId = $this->wishlistHelper->getWishlist()->getId();
        if ($this->getRequest()->isAjax()) {
            return $this->processAjax($params, $wishlistId);
        }

        return $this->processRequest($params, $wishlistId);
    }

    /**
     * Process ajax request
     * @param array $params
     * @param int $wishlistId
     * @return Json
     */
    protected function processAjax(array $params, int $wishlistId)
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        if (!$wishlistId || !isset($params['name']) || !trim($params['name'])) {
            $resultJson->setData(
                [
                    'success' => false,
                    'message' => __('Required data missing.')
                ]
            );
            return $resultJson;
        }

        $create = $this->createWishlist($params, $wishlistId);

        if (!$create) {
            $resultJson->setData(
                [
                    'success' => false,
                    'message' => __('Something went wrong while saving the wishlist.')
                ]
            );
            return $resultJson;
        }

        $resultJson->setData(
            [
                'success' => true,
                'message' => __('Wishlist has been successfully saved.')
            ]
        );
        return $resultJson;
    }

    /**
     * Process request
     * @param array $params
     * @param int $wishlistId
     * @return Redirect
     */
    protected function processRequest(array $params, int $wishlistId)
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if (!$wishlistId || !isset($params['name']) || !trim($params['name'])) {
            $this->messageManager->addErrorMessage(__('Required data missing.'));
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        $create = $this->createWishlist($params, $wishlistId);

        if (!$create) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the wishlist.'));
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        $this->messageManager->addSuccessMessage(__('Wishlist has been successfully saved.'));
    }

    /**
     * Creates a multiple wishlist
     * @param array $params
     * @param int $wishlistId
     * @return bool
     */
    protected function createWishlist(array $params, int $wishlistId)
    {
        $multipleWishlist = $this->multipleWishlistFactory->create();
        $multipleWishlist->setWishlistId($wishlistId);
        $multipleWishlist->setName($params['name']);

        try {
            $this->multipleWishlistRepository->save($multipleWishlist);
        } catch (CouldNotSaveException $e) {
            return false;
        }

        return true;
    }
}
