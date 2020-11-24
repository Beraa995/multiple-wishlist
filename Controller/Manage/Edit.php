<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Controller\Manage;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Controller\AbstractIndex;

/**
 * Multiple wishlist edit controller
 */
class Edit extends AbstractIndex implements HttpGetActionInterface
{
    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param Data $moduleHelper
     */
    public function __construct(
        Context $context,
        Data $moduleHelper
    ) {
        parent::__construct($context);
        $this->moduleHelper = $moduleHelper;
    }

    public function execute()
    {
        if (!$this->moduleHelper->isEnabled()) {
            $this->_forward('noroute');
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $multipleWishlist = $this->getRequest()->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);

        if ($multipleWishlist) {
            //@TODO Use wishlist name in the title
            $resultPage->getConfig()->getTitle()->set(__('Edit Wishlist'));
        } else {
            $resultPage->getConfig()->getTitle()->set(__('Create New Wishlist'));
        }

        return $resultPage;
    }
}
