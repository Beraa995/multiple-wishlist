<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Controller\Manage;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Wishlist\Controller\AbstractIndex;

/**
 * Multiple wishlist new controller
 */
class NewAction extends AbstractIndex implements HttpGetActionInterface
{
    /**
     * Forwards to edit route
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
