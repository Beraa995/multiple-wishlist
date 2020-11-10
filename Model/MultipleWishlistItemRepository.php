<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Model;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistItemSearchResultsInterfaceFactory;
use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistItemSearchResultsInterface;
use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistItemInterface;
use BKozlic\MultipleWishlist\Api\MultipleWishlistItemRepositoryInterface;
use BKozlic\MultipleWishlist\Model\ResourceModel\MultipleWishlistItem as MultipleWishlistItemResource;
use BKozlic\MultipleWishlist\Model\ResourceModel\MultipleWishlistItem\CollectionFactory as MultipleWishlistItemCollection;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class MultipleWishlistItemRepository
 */
class MultipleWishlistItemRepository implements MultipleWishlistItemRepositoryInterface
{
    /**
     * @var MultipleWishlistItemResource
     */
    protected $resource;

    /**
     * @var MultipleWishlistItemFactory
     */
    protected $multipleWishlistItemFactory;

    /**
     * @var MultipleWishlistItemCollection
     */
    protected $collectionFactory;

    /**
     * @var MultipleWishlistItemSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * MultipleWishlistItemRepository constructor.
     * @param MultipleWishlistItemSearchResultsInterfaceFactory $searchResultsFactory
     * @param MultipleWishlistItemFactory $multipleWishlistItemFactory
     * @param MultipleWishlistItemCollection $collectionFactory
     * @param MultipleWishlistItemResource $resource
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        MultipleWishlistItemSearchResultsInterfaceFactory $searchResultsFactory,
        MultipleWishlistItemFactory $multipleWishlistItemFactory,
        MultipleWishlistItemCollection $collectionFactory,
        MultipleWishlistItemResource $resource,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
        $this->resource = $resource;
        $this->multipleWishlistFactory = $multipleWishlistItemFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * Save multiple wishlist item data
     *
     * @param MultipleWishlistItemInterface $multipleWishlistItem
     * @throws CouldNotSaveException
     * @return MultipleWishlistItem
     */
    public function save(MultipleWishlistItemInterface $multipleWishlistItem)
    {
        try {
            $this->resource->save($multipleWishlistItem);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $multipleWishlistItem;
    }

    /**
     * Load multiple wishlist item by id
     *
     * @param int $multipleWishlistItemId
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return MultipleWishlistItem
     */
    public function get(int $multipleWishlistItemId)
    {
        $multipleWishlistItem = $this->multipleWishlistItemFactory->create();
        $this->resource->load($multipleWishlistItem, $multipleWishlistItemId);

        if (!$multipleWishlistItem->getId()) {
            throw new NoSuchEntityException(__(
                'The multiple wishlist item record with the "%1" ID doesn\'t exist.',
                $multipleWishlistItemId
            ));
        }

        return $multipleWishlistItem;
    }

    /**
     * Load multiple wishlist item data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return MultipleWishlistItemSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->collectionFactory->create();

        $this->extensionAttributesJoinProcessor->process($collection);
        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Delete multiple wishlist item
     *
     * @param MultipleWishlistItemInterface $multipleWishlistItem
     * @throws CouldNotDeleteException
     * @return bool
     */
    public function delete(MultipleWishlistItemInterface $multipleWishlistItem)
    {
        try {
            $this->resource->delete($multipleWishlistItem);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * Delete multiple wishlist item by ID
     *
     * @param int $multipleWishlistItemId
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return bool
     */
    public function deleteById(int $multipleWishlistItemId)
    {
        return $this->delete($this->get($multipleWishlistItemId));
    }
}
