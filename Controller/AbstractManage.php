<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Controller;

use BKozlic\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\UrlInterface;

/**
 * Abstract class for multiple wishlist manage controllers
 */
abstract class AbstractManage extends Action
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var MultipleWishlistRepositoryInterface
     */
    protected $multipleWishlistRepository;

    /**
     * AbstractManage constructor.
     *
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param Validator $formKeyValidator
     * @param MultipleWishlistRepositoryInterface $multipleWishlistRepository
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        Validator $formKeyValidator,
        MultipleWishlistRepositoryInterface $multipleWishlistRepository
    ) {
        parent::__construct($context);
        $this->urlBuilder = $urlBuilder;
        $this->formKeyValidator = $formKeyValidator;
        $this->multipleWishlistRepository = $multipleWishlistRepository;
    }

    /**
     * Process request
     *
     * @param $message
     * @param bool $success
     * @param null|string $referer
     * @return Json|Redirect
     */
    protected function processReturn($message, $success = true, $referer = null)
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
            $resultRedirect->setPath($this->_redirect->getRefererUrl());
        } else {
            if ($referer) {
                $resultRedirect->setPath($referer);
            } else {
                $resultRedirect->setPath($this->urlBuilder->getUrl('*/*/index'));
            }

            $this->messageManager->addSuccessMessage($message);
        }

        return $resultRedirect;
    }
}
