<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Api\Data;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistItemExtensionInterface;
use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface for MultipleWishlistItem Model
 */
interface MultipleWishlistItemInterface extends ExtensibleDataInterface
{
    const PRIMARY_ID = 'item_id';
    const MULTIPLE_WISHLIST_ID = 'multiple_wishlist_id';
    const MULTIPLE_WISHLIST_ITEM = 'wishlist_item_id';

    /**
     * Returns multiple wishlist item id
     *
     * @return int
     */
    public function getId();

    /**
     * Set multiple wishlist item id
     *
     * @param int $id
     * @return MultipleWishlistItemInterface
     */
    public function setId($id);

    /**
     * Returns multiple wishlist id
     *
     * @return int
     */
    public function getMultipleWishlistId();

    /**
     * Set multiple wishlist id
     *
     * @param int|null $id
     * @return MultipleWishlistItemInterface
     */
    public function setMultipleWishlistId($id);

    /**
     * Returns main wishlist item id
     *
     * @return int
     */
    public function getWishlistItemId();

    /**
     * Set main wishlist item id
     *
     * @param int $id
     * @return MultipleWishlistItemInterface
     */
    public function setWishlistItemId(int $id);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return MultipleWishlistItemExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param MultipleWishlistItemExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(MultipleWishlistItemExtensionInterface $extensionAttributes);
}
