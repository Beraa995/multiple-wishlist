<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Observer;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
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
class AddProductToWishlistObserver implements ObserverInterface
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
        //@TODO After add/remove/update recalculate main item qty
        //@TODO Check if checking isEnabled is used on every customization
        //@TODO Add multiple wishlist param to the update wishlist button on product detail
        if (!$this->moduleHelper->isEnabled()) {
            return;
        }

        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        $qty = $this->request->getParam('qty') ?: 1;
        $items = $observer->getEvent()->getItems();

        foreach ($items as $item) {
            if (!$item || !$item->getId()) {
                continue;
            }

            $itemId = $item->getId();
            $existingItem = $this->getExistingItem($itemId, $multipleWishlist);

            if ($existingItem) {
                try {
                    $existingItem->setQty($existingItem->getQty() + $qty);
                    $this->itemRepository->save($existingItem);
                } catch (CouldNotSaveException $e) {
                    $this->logger->error($e->getMessage());
                }
            } else {
                $this->processNewItemCreation($itemId, $multipleWishlist, $qty);
            }

            $this->moduleHelper->recalculate($itemId);
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
        $itemList = $this->moduleHelper->getMultipleWishlistItems($multipleWishlistId, $itemId);
        if (!count($itemList)) {
            return null;
        }

        return array_shift($itemList);
    }
}
