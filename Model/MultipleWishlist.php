<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Model;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistExtensionInterface;
use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Model class for Multiple Wishlist
 */
class MultipleWishlist extends AbstractExtensibleModel implements MultipleWishlistInterface, IdentityInterface
{
    const CACHE_TAG = 'multiple_wishlist';

    /**
     * @var string
     */
    protected $_idFieldName = self::MULTIPLE_WISHLIST_ID;

    /**
     * @var string
     */
    protected $_cacheTag = 'multiple_wishlist';

    /**
     * @var string
     */
    protected $_eventPrefix = 'multiple_wishlist';

    /**
     * @inheridoc
     */
    public function _construct()
    {
        $this->_init(ResourceModel\MultipleWishlist::class);
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
    public function getWishlistId()
    {
        return $this->getData(self::WISHLIST_ID);
    }

    /**
     * @inheritDoc
     */
    public function setWishlistId(int $id)
    {
        return $this->setData(self::WISHLIST_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->getData(self::MULTIPLE_WISHLIST_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name)
    {
        return $this->setData(self::MULTIPLE_WISHLIST_NAME, $name);
    }

    /**
     * @inheritDoc
     */
    public function getSharingCode()
    {
        return $this->getData(self::MULTIPLE_WISHLIST_SHARING_CODE);
    }

    /**
     * @inheritDoc
     */
    public function setSharingCode(string $code)
    {
        return $this->setData(self::MULTIPLE_WISHLIST_SHARING_CODE, $code);
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
    public function setExtensionAttributes(MultipleWishlistExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
