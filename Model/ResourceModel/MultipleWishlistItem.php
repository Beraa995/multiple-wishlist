<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class MultipleWishlistItem extends AbstractDb
{
    /**
     * @inheridoc
     */
    public function _construct()
    {
        $this->_init('multiple_wishlist_item', 'item_id');
    }
}
