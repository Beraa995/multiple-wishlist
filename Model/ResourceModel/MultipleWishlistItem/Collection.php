<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Model\ResourceModel\MultipleWishlistItem;

use BKozlic\MultipleWishlist\Model\MultipleWishlistItem as MultipleWishlistItemModel;
use BKozlic\MultipleWishlist\Model\ResourceModel\MultipleWishlistItem as MultipleWishlistItemResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'multiple_wishlist_item_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'multiple_wishlist_item_collection';

    /**
     * @inheridoc
     */
    public function _construct()
    {
        $this->_init(MultipleWishlistItemModel::class, MultipleWishlistItemResource::class);
    }
}
