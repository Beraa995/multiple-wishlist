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
 * Interface MultipleWishlistSearchResultsInterface
 */
interface MultipleWishlistSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get multiple wishlist list.
     *
     * @return MultipleWishlistInterface[]
     */
    public function getItems();

    /**
     * Set multiple wishlist.
     *
     * @param MultipleWishlistInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
