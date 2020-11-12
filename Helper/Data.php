<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Helper;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistItemInterface;
use BKozlic\MultipleWishlist\Api\MultipleWishlistItemRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

/**
 * Module's helper class
 */
class Data extends AbstractHelper
{
    /**
     * Helper constants
     */
    const XML_PATH_ENABLED = 'wishlist/multiple_wishlist_general/enabled';
    const XML_PATH_STRATEGY = 'wishlist/multiple_wishlist_general/wishlist_strategy';

    /**
     * @var MultipleWishlistItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Data constructor.
     * @param Context $context
     * @param MultipleWishlistItemRepositoryInterface $itemRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        MultipleWishlistItemRepositoryInterface $itemRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->itemRepository = $itemRepository;
        $this->logger = $logger;
    }

    /**
     * Checks if module is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Checks if wishlist modal can be shown or not
     *
     * @return bool
     */
    public function canShowModal()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_STRATEGY, ScopeInterface::SCOPE_STORE);
    }

    public function recalculate()
    {
        //@TODO Add logic for main item recalculation
    }

    /**
     * Makes unique multiple wishlist item collection if there are duplicates
     *
     * @param $collection
     * @return array
     */
    public function makeUniqueCollection($collection)
    {
        $items = [];
        /** @var MultipleWishlistItemInterface $multipleWishlistItem */
        foreach ($collection as $multipleWishlistItem) {
            $multipleWishlist = $multipleWishlistItem->getMultipleWishlistId() ?: '0';
            $key = $multipleWishlistItem->getWishlistItemId() . $multipleWishlist;

            if (!in_array($key, array_keys($items))) {
                $items[$key] = $multipleWishlistItem;
            } else {
                $existing = $items[$key];

                try {
                    $this->itemRepository->delete($multipleWishlistItem);
                    $existing->setQty($existing->getQty() + $multipleWishlistItem->getQty());
                    $items[$key] = $existing->setData('changed', true);
                } catch (CouldNotDeleteException $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }

        foreach ($items as $key => $item) {
            if ($item->getData('changed')) {
                try {
                    $this->itemRepository->save($item);
                } catch (CouldNotSaveException $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        }

        return array_values($items);
    }
}
