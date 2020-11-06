<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Model\ResourceModel\MultipleWishlist;

use BKozlic\MultipleWishlist\Model\MultipleWishlist as MultipleWishlistModel;
use BKozlic\MultipleWishlist\Model\ResourceModel\MultipleWishlist as MultipleWishlistResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'multiple_wishlist_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'multiple_wishlist_collection';

    /**
     * @inheridoc
     */
    public function _construct()
    {
        $this->_init(MultipleWishlistModel::class, MultipleWishlistResource::class);
    }
}
