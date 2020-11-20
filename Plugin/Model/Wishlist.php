<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Plugin\Model;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Model\ResourceModel\Item\Collection;
use Magento\Wishlist\Model\Wishlist as MagentoWishlistModel;

/**
 * Filter item collection class
 */
class Wishlist
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
     * @var WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * Wishlist Model Plugin constructor.
     *
     * @param RequestInterface $request
     * @param Data $moduleHelper
     * @param WishlistProviderInterface $wishlistProvider
     */
    public function __construct(
        RequestInterface $request,
        Data $moduleHelper,
        WishlistProviderInterface $wishlistProvider
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->request = $request;
        $this->wishlistProvider = $wishlistProvider;
    }

    /**
     * Process filtering collection by custom multiple wishlist id
     *
     * @param MagentoWishlistModel $subject
     * @param Collection $result
     * @return Collection
     */
    public function afterGetItemCollection(MagentoWishlistModel $subject, Collection $result)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $multipleWishlistId = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        $ids = $this->getMultipleWishlistItemIds($multipleWishlistId);

        $result->addFieldToFilter('wishlist_item_id', ['in' => $ids]);
        return $result;
    }

    /**
     * Returns ids of items in the specified multiple wishlist with description and qty
     *
     * @param int|null $multipleWishlistId
     * @return array
     */
    protected function getMultipleWishlistItemIds($multipleWishlistId)
    {
        $itemList = $this->moduleHelper->getMultipleWishlistItems($multipleWishlistId);

        /**
         * If there are no items in the default wishlist, than the items
         * from the first wishlist will be returned. This is because
         * wishlist switcher is not showing empty default wishlist.
         * The sort must not be applied in the multiple wishlist switcher.
         */
        if (!$multipleWishlistId && !count($itemList)) {
            $wishlist = $this->wishlistProvider->getWishlist()->getId();
            $firstWishlist = $this->moduleHelper->getFirstMultipleWishlist($wishlist);

            if ($firstWishlist) {
                $itemList = $this->moduleHelper->getMultipleWishlistItems(
                    $firstWishlist->getId()
                );
            }
        }

        $ids = [];
        foreach ($itemList as $item) {
            $ids[] = $item->getWishlistItemId();
        }

        return $ids;
    }
}
