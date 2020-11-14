<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Plugin\Model\Wishlist;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Api\MultipleWishlistItemRepositoryInterface;
use BKozlic\MultipleWishlist\Helper\Data;
use Closure;
use Magento\Checkout\Model\Cart;
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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Item Plugin constructor.
     * @param RequestInterface $request
     * @param MultipleWishlistItemRepositoryInterface $itemRepository
     * @param LoggerInterface $logger
     * @param Data $moduleHelper
     */
    public function __construct(
        RequestInterface $request,
        MultipleWishlistItemRepositoryInterface $itemRepository,
        LoggerInterface $logger,
        Data $moduleHelper
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->request = $request;
        $this->itemRepository = $itemRepository;
        $this->logger = $logger;
    }

    /**
     * Changes delete argument to prevent wishlist item removal by default
     *
     * @param WishlistItem $subject
     * @param Cart $cart
     * @param bool $delete
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
     * If item doesn't belong to the wishlist prevent default
     *
     * @param WishlistItem $subject
     * @param Closure $proceed
     * @param Cart $cart
     * @param bool $delete
     * @return bool
     */
    public function aroundAddToCart(WishlistItem $subject, Closure $proceed, $cart, $delete)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $proceed($cart, $delete);
        }

        $itemList = $this->getItemList($subject->getId());
        if (!count($itemList)) {
            return false;
        }

        return $proceed($cart, $delete);
    }

    /**
     * Add logic for multiple wishlist item removal after add to cart
     *
     * @param WishlistItem $subject
     * @param bool $result
     * @param Cart $cart
     * @param bool $delete
     * @return bool
     */
    public function afterAddToCart(WishlistItem $subject, $result, $cart, $delete)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        if (!$result) {
            return $result;
        }

        $itemList = $this->getItemList($subject->getId());
        foreach ($itemList as $item) {
            try {
                $this->itemRepository->delete($item);
                $this->moduleHelper->recalculate($item->getWishlistItemId());
            } catch (CouldNotDeleteException $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return $result;
    }

    /**
     * Returns multiple wishlist item collection filtered by wishlist item id
     *
     * @param $id
     * @return array
     */
    protected function getItemList($id)
    {
        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);

        return $this->moduleHelper->getMultipleWishlistItems($multipleWishlist, $id);
    }
}
