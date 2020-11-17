<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Plugin\Model;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Wishlist\Model\Wishlist as MagentoWishlistModel;
use Magento\Wishlist\Model\ResourceModel\Item\Collection;

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
     * Item Plugin constructor.
     * @param RequestInterface $request
     * @param Data $moduleHelper
     */
    public function __construct(
        RequestInterface $request,
        Data $moduleHelper
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->request = $request;
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
        $idToQtyMapper = $this->getMultipleWishlistItemIdToQtyMapper($multipleWishlistId);

        $result->addFieldToFilter('wishlist_item_id', ['in' => array_keys($idToQtyMapper)]);

        foreach ($result as $wishlistItem) {
            if (isset($idToQtyMapper[$wishlistItem->getId()])) {
                $wishlistItem->setQty($idToQtyMapper[$wishlistItem->getId()]['qty']);
                $wishlistItem->setDescription($idToQtyMapper[$wishlistItem->getId()]['desc']);
            }
        }

        return $result;
    }

    /**
     * Returns ids of items in the specified multiple wishlist with description and qty
     *
     * @param int|null $multipleWishlistId
     * @return array
     */
    protected function getMultipleWishlistItemIdToQtyMapper($multipleWishlistId)
    {
        //@TODO If there are no items to the default wishlist use first one from the list
        $itemList = $this->moduleHelper->getMultipleWishlistItems($multipleWishlistId);
        $ids = [];
        foreach ($itemList as $item) {
            $ids[$item->getWishlistItemId()]['qty'] = $item->getQty();
            $ids[$item->getWishlistItemId()]['desc'] = $item->getDescription();
        }

        return $ids;
    }
}
