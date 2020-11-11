<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Observer;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistItemInterface;
use BKozlic\MultipleWishlist\Api\MultipleWishlistItemRepositoryInterface;
use BKozlic\MultipleWishlist\Helper\Data;
use BKozlic\MultipleWishlist\Model\MultipleWishlistItemFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;

/**
 * Process multiple wishlist data after item is added to the wishlist
 */
class AddToWishlistObserver implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var MultipleWishlistItemFactory
     */
    protected $itemFactory;

    /**
     * @var MultipleWishlistItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * AddToWishlistObserver constructor.
     * @param RequestInterface $request
     * @param MultipleWishlistItemFactory $itemFactory
     * @param MultipleWishlistItemRepositoryInterface $itemRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     * @param Data $moduleHelper
     */
    public function __construct(
        RequestInterface $request,
        MultipleWishlistItemFactory $itemFactory,
        MultipleWishlistItemRepositoryInterface $itemRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger,
        Data $moduleHelper
    ) {
        $this->request = $request;
        $this->itemFactory = $itemFactory;
        $this->itemRepository = $itemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Process multiple wishlist item create/update
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return;
        }

        $multipleWishlist = $this->request->getParam('multiple_wishlist_id');
        $qty = $this->request->getParam('qty') ?: 1;
        $mainItem = $observer->getEvent()->getItem();

        if (!$mainItem || !$mainItem->getId()) {
            return;
        }

        $mainItemId = $mainItem->getId();
        $existingItem = $this->getExistingItem($mainItemId, $multipleWishlist);

        if ($existingItem) {
            try {
                $existingItem->setQty($existingItem->getQty() + $qty);
                $this->itemRepository->save($existingItem);
            } catch (CouldNotSaveException $e) {
                $this->logger->error($e->getMessage());
            }
        } else {
            $this->processNewItemCreation($mainItemId, $multipleWishlist, $qty);
        }
    }

    /**
     * Save new item for multiple wishlist
     *
     * @param int $itemId
     * @param int|null $multipleWishlistId
     * @param float $qty
     * @return void
     */
    protected function processNewItemCreation(int $itemId, $multipleWishlistId, float $qty)
    {
        $multipleWishlistItem = $this->itemFactory->create();
        $multipleWishlistItem->setWishlistItemId($itemId);
        $multipleWishlistItem->setQty($qty);

        if ($multipleWishlistId) {
            $multipleWishlistItem->setMultipleWishlistId($multipleWishlistId);
        }

        try {
            $this->itemRepository->save($multipleWishlistItem);
        } catch (CouldNotSaveException $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * Returns existing item(s) with the same wishlist id and wishlist item id
     *
     * @param int $itemId
     * @param int|null $multipleWishlistId
     * @return MultipleWishlistItemInterface|null
     */
    protected function getExistingItem(int $itemId, $multipleWishlistId)
    {
        $this->searchCriteriaBuilder->addFilter(
            MultipleWishlistItemInterface::MULTIPLE_WISHLIST_ID,
            $multipleWishlistId,
            $multipleWishlistId ? 'eq' : 'null'
        );

        $this->searchCriteriaBuilder->addFilter(
            MultipleWishlistItemInterface::MULTIPLE_WISHLIST_ITEM,
            $itemId
        );

        $itemList = $this->itemRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        if (!count($itemList)) {
            return null;
        }

        if (count($itemList) > 1) {
            $firstItem = array_shift($itemList);

            foreach ($itemList as $item) {
                try {
                    $firstItem->setQty($item->getQty() + $firstItem->getQty());
                    $this->itemRepository->delete($item);
                } catch (CouldNotDeleteException $e) {
                    $this->logger->error($e->getMessage());
                }
            }

            return $firstItem;
        }

        return array_shift($itemList);
    }
}
