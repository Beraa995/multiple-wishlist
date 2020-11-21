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
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use Magento\Wishlist\Helper\Data as WishlistHelper;
use Psr\Log\LoggerInterface;

/**
 * Controller for multiple wishlist creation
 */
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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Random
     */
    protected $mathRandom;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * Create constructor.
     * @param Context $context
     * @param MultipleWishlistFactory $multipleWishlistFactory
     * @param MultipleWishlistRepository $multipleWishlistRepository
     * @param WishlistHelper $wishlistHelper
     * @param LoggerInterface $logger
     * @param Random $mathRandom
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Context $context,
        MultipleWishlistFactory $multipleWishlistFactory,
        MultipleWishlistRepository $multipleWishlistRepository,
        WishlistHelper $wishlistHelper,
        LoggerInterface $logger,
        Random $mathRandom,
        Validator $formKeyValidator
    ) {
        parent::__construct($context);
        $this->multipleWishlistFactory = $multipleWishlistFactory;
        $this->multipleWishlistRepository = $multipleWishlistRepository;
        $this->wishlistHelper = $wishlistHelper;
        $this->logger = $logger;
        $this->mathRandom = $mathRandom;
        $this->formKeyValidator = $formKeyValidator;
    }

    /**
     * Process multiple wishlist creation
     *
     * @throws LocalizedException
     * @return Json|Redirect
     */
    public function execute()
    {
        //@TODO Limit number of wishlists with system configuration
        //@TODO Add form key and validate here and in Delete controller
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
     * Process request
     *
     * @param $message
     * @param bool $success
     * @return Json|Redirect
     */
    protected function processReturn($message, $success = true)
    {
        /**
         * @var Json $resultJson
         * @var Redirect $resultRedirect
         */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($this->getRequest()->isAjax()) {
            $resultJson->setData(
                [
                    'success' => $success,
                    'message' => $message
                ]
            );
            return $resultJson;
        }

        if (!$success) {
            $this->messageManager->addErrorMessage($message);
        } else {
            $this->messageManager->addSuccessMessage($message);
        }

        return $resultRedirect->setPath($this->_redirect->getRefererUrl());
    }

    /**
     * Creates a multiple wishlist
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
