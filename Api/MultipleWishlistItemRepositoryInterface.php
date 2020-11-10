<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Api;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistItemInterface;
use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistItemSearchResultsInterface;
use BKozlic\MultipleWishlist\Model\MultipleWishlistItem;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface for MultipleWishlistItemRepository
 */
interface MultipleWishlistItemRepositoryInterface
{
    /**
     * Save multiple wishlist item data
     * @param MultipleWishlistItemInterface $multipleWishlistItem
     * @throws CouldNotSaveException
     * @return MultipleWishlistItem
     */
    public function save(MultipleWishlistItemInterface $multipleWishlistItem);

    /**
     * Load multiple wishlist item by id
     * @param int $multipleWishlistItemId
     * @throws NoSuchEntityException
     * @return MultipleWishlistItem
     */
    public function get(int $multipleWishlistItemId);

    /**
     * Load multiple wishlist item data collection by given search criteria
     * @param SearchCriteriaInterface $criteria
     * @return MultipleWishlistItemSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * Delete multiple wishlist item
     * @param MultipleWishlistItemInterface $multipleWishlistItem
     * @throws CouldNotDeleteException
     * @return bool
     */
    public function delete(MultipleWishlistItemInterface $multipleWishlistItem);

    /**
     * Delete multiple wishlist item by id
     * @param int $multipleWishlistItemId
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     * @return bool
     */
    public function deleteById(int $multipleWishlistItemId);
}
