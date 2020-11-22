<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Controller\Manage;

use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Wishlist\Controller\AbstractIndex;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Wishlist\Helper\Data as WishlistHelper;

/**
 * Multiple wishlist index controller
 */
class Index extends AbstractIndex implements HttpGetActionInterface
{
    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * Index constructor.
     * @param Context $context
     * @param Data $moduleHelper
     * @param WishlistHelper $wishlistHelper
     */
    public function __construct(
        Context $context,
        Data $moduleHelper,
        WishlistHelper $wishlistHelper
    ) {
        parent::__construct($context);
        $this->moduleHelper = $moduleHelper;
        $this->wishlistHelper = $wishlistHelper;
    }

    public function execute()
    {
        if (!$this->moduleHelper->isEnabled()) {
            $this->_forward('noroute');
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set(__('Manage Wishlists'));
        return $resultPage;
    }
}
