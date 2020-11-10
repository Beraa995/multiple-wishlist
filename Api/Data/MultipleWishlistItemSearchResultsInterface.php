<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface MultipleWishlistItemSearchResultsInterface
 */
interface MultipleWishlistItemSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get multiple wishlist item list.
     *
     * @return MultipleWishlistItemInterface[]
     */
    public function getItems();

    /**
     * Set multiple wishlist item list.
     *
     * @param MultipleWishlistItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
