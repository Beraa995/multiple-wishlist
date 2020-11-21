<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;

/**
 * Abstract class for multiple wishlist manage controllers
 */
abstract class AbstractManage extends Action
{
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
}
