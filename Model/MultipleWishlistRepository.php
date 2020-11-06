<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Model;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistSearchResultsInterfaceFactory;
use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistSearchResultsInterface;
use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use BKozlic\MultipleWishlist\Model\ResourceModel\MultipleWishlist as MultipleWishlistResource;
use BKozlic\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory as MultipleWishlistCollection;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class MultipleWishlistRepository
 */
class MultipleWishlistRepository implements MultipleWishlistRepositoryInterface
{
    /**
     * @var MultipleWishlistResource
     */
    protected $resource;

    /**
     * @var MultipleWishlistFactory
     */
    protected $multipleWishlistFactory;

    /**
     * @var MultipleWishlistCollection
     */
    protected $collectionFactory;

    /**
     * @var MultipleWishlistSearchResultsInterfaceFactory
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
     * MultipleWishlistRepository constructor.
     * @param MultipleWishlistSearchResultsInterfaceFactory $searchResultsFactory
     * @param MultipleWishlistFactory $multipleWishlistFactory
     * @param MultipleWishlistCollection $collectionFactory
     * @param MultipleWishlistResource $resource
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        MultipleWishlistSearchResultsInterfaceFactory $searchResultsFactory,
        MultipleWishlistFactory $multipleWishlistFactory,
        MultipleWishlistCollection $collectionFactory,
        MultipleWishlistResource $resource,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
        $this->resource = $resource;
        $this->multipleWishlistFactory = $multipleWishlistFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * Save multiple wishlist data
     *
     * @param MultipleWishlistInterface $multipleWishlist
     * @throws CouldNotSaveException
     * @return MultipleWishlist
     */
    public function save(MultipleWishlistInterface $multipleWishlist)
    {
        try {
            $this->resource->save($multipleWishlist);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $multipleWishlist;
    }

    /**
     * Load multiple wishlist by id
     *
     * @param int $multipleWishlistId
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return MultipleWishlist
     */
    public function get(int $multipleWishlistId)
    {
        $multipleWishlist = $this->multipleWishlistFactory->create();
        $this->resource->load($multipleWishlist, $multipleWishlistId);

        if (!$multipleWishlist->getId()) {
            throw new NoSuchEntityException(__('The multiple wishlist record with the "%1" ID doesn\'t exist.', $multipleWishlistId));
        }

        return $multipleWishlist;
    }

    /**
     * Load multiple wishlist data collection by given search criteria
     *
     * @param SearchCriteriaInterface $criteria
     * @return MultipleWishlistSearchResultsInterface
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
     * Delete multiple wishlist
     *
     * @param MultipleWishlistInterface $multipleWishlist
     * @throws CouldNotDeleteException
     * @return bool
     */
    public function delete(MultipleWishlistInterface $multipleWishlist)
    {
        try {
            $this->resource->delete($multipleWishlist);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * Delete multiple wishlist by ID
     *
     * @param int $multipleWishlistId
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return bool
     */
    public function deleteById(int $multipleWishlistId)
    {
        return $this->delete($this->get($multipleWishlistId));
    }
}
