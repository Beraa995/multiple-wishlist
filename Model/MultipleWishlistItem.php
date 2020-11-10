<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Model;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistItemExtensionInterface;
use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistItemInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Model class for Multiple Wishlist Item
 */
class MultipleWishlistItem extends AbstractExtensibleModel implements IdentityInterface, MultipleWishlistItemInterface
{
    const CACHE_TAG = 'multiple_wishlist_item';

    /**
     * @var string
     */
    protected $_idFieldName = self::PRIMARY_ID;

    /**
     * @var string
     */
    protected $_cacheTag = 'multiple_wishlist_item';

    /**
     * @var string
     */
    protected $_eventPrefix = 'multiple_wishlist_item';

    /**
     * @inheridoc
     */
    public function _construct()
    {
        $this->_init(ResourceModel\MultipleWishlistItem::class);
    }

    /**
     * @inheridoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritDoc
     */
    public function getMultipleWishlistId()
    {
        return $this->getData(self::MULTIPLE_WISHLIST_ID);
    }

    /**
     * @inheritDoc
     */
    public function setMultipleWishlistId($id)
    {
        return $this->setData(self::MULTIPLE_WISHLIST_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getWishlistItemId()
    {
        return $this->getData(self::MULTIPLE_WISHLIST_ITEM);
    }

    /**
     * @inheritDoc
     */
    public function setWishlistItemId(int $id)
    {
        return $this->setData(self::MULTIPLE_WISHLIST_ITEM, $id);
    }

    /**
     * @inheritDoc
     */
    public function getQty()
    {
        return $this->getData(self::MULTIPLE_WISHLIST_ITEM_QTY);
    }

    /**
     * @inheritDoc
     */
    public function setQty(float $qty)
    {
        return $this->setData(self::MULTIPLE_WISHLIST_ITEM_QTY, $qty);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(MultipleWishlistItemExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
