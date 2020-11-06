<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Api\Data;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistExtensionInterface;
use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface MultipleWishlistInterface
 */
interface MultipleWishlistInterface extends ExtensibleDataInterface
{
    const MULTIPLE_WISHLIST_ID = 'multiple_wishlist_id';
    const WISHLIST_ID = 'wishlist_id';
    const MULTIPLE_WISHLIST_NAME = 'name';

    /**
     * Returns multiple wishlist id
     *
     * @return int
     */
    public function getId();

    /**
     * Set multiple wishlist id
     *
     * @param int $id
     * @return MultipleWishlistInterface
     */
    public function setId($id);

    /**
     * Returns main wishlist id
     *
     * @return int
     */
    public function getWishlistId();

    /**
     * Set main wishlist id
     *
     * @param int $id
     * @return MultipleWishlistInterface
     */
    public function setWishlistId(int $id);

    /**
     * Returns multiple wishlist name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set multiple wishlist name
     *
     * @param string $name
     * @return MultipleWishlistInterface
     */
    public function setName(string $name);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return MultipleWishlistExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param MultipleWishlistExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(MultipleWishlistExtensionInterface $extensionAttributes);
}
