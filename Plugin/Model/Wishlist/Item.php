<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Plugin\Model\Wishlist;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistItemInterface;
use BKozlic\MultipleWishlist\Api\MultipleWishlistItemRepositoryInterface;
use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Wishlist\Model\Item as WishlistItem;
use Psr\Log\LoggerInterface;

/**
 * Plugin class for changing add to cart logic in the wishlist
 */
class Item
{
    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * @var RequestInterface
     */
    protected $request;

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
     * Item Plugin constructor.
     * @param RequestInterface $request
     * @param MultipleWishlistItemRepositoryInterface $itemRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param LoggerInterface $logger
     * @param Data $moduleHelper
     */
    public function __construct(
        RequestInterface $request,
        MultipleWishlistItemRepositoryInterface $itemRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger,
        Data $moduleHelper
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->request = $request;
        $this->itemRepository = $itemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger = $logger;
    }

    /**
     * Changes delete argument to prevent wishlist item removal by default
     *
     * @param WishlistItem $subject
     * @param $cart
     * @param $delete
     * @return array
     */
    public function beforeAddToCart(WishlistItem $subject, $cart, $delete)
    {
        if ($this->moduleHelper->isEnabled()) {
            $delete = false;
        }

        return [$cart, $delete];
    }

    /**
     * Add logic for multiple wishlist item removal after add to cart
     *
     * @param WishlistItem $subject
     * @param $result
     * @return bool
     */
    public function afterAddToCart(WishlistItem $subject, $result)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        if (!$result) {
            return $result;
        }

        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        $itemId = $subject->getId();

        $this->searchCriteriaBuilder->addFilter(
            MultipleWishlistItemInterface::MULTIPLE_WISHLIST_ID,
            $multipleWishlist,
            $multipleWishlist ? 'eq' : 'null'
        );

        $this->searchCriteriaBuilder->addFilter(
            MultipleWishlistItemInterface::MULTIPLE_WISHLIST_ITEM,
            $itemId
        );
        $itemList = $this->itemRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        $uniqueList = $this->moduleHelper->makeUniqueCollection($itemList);

        foreach ($uniqueList as $item) {
            try {
                $this->itemRepository->delete($item);
                $this->moduleHelper->recalculate($item->getWishlistItemId());
            } catch (CouldNotDeleteException $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
