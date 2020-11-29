<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Controller\Manage;

use BKozlic\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use BKozlic\MultipleWishlist\Controller\AbstractManage;
use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;
use Psr\Log\LoggerInterface;

/**
 * Controller for multiple wishlist edit action
 */
class EditPost extends AbstractManage implements HttpPostActionInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * EditPost constructor.
     *
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param MultipleWishlistRepositoryInterface $multipleWishlistRepository
     * @param LoggerInterface $logger
     * @param Validator $formKeyValidator
     * @param WishlistHelper $wishlistHelper
     * @param Data $moduleHelper
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        MultipleWishlistRepositoryInterface $multipleWishlistRepository,
        LoggerInterface $logger,
        Validator $formKeyValidator,
        WishlistHelper $wishlistHelper,
        Data $moduleHelper
    ) {
        parent::__construct(
            $context,
            $urlBuilder,
            $formKeyValidator,
            $multipleWishlistRepository
        );
        $this->logger = $logger;
        $this->moduleHelper = $moduleHelper;
        $this->wishlistHelper = $wishlistHelper;
    }

    /**
     * Process multiple wishlist edit action
     *
     * @return Json|Redirect
     */
    public function execute()
    {
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

        if (!isset($params['id'])) {
            return $this->processReturn(
                __('Something went wrong.'),
                false
            );
        }

        $multipleWishlist = $this->moduleHelper->getMultipleWishlist($params['id']);
        if (!$multipleWishlist || $multipleWishlist->getWishlistId() !== $wishlistId) {
            return $this->processReturn(
                __('Something went wrong.'),
                false
            );
        }

        try {
            $multipleWishlist->setName($params['name']);
            $this->multipleWishlistRepository->save($multipleWishlist);
        } catch (CouldNotSaveException $e) {
            $this->logger->error($e->getMessage());
            return $this->processReturn(
                __('Something went wrong.'),
                false
            );
        }

        return $this->processReturn(
            __('Wishlist has been successfully changed.')
        );
    }
}
