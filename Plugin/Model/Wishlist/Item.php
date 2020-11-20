<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Plugin\Model\Wishlist;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Magento\Wishlist\Model\Item as WishlistItem;

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
     * Returns multiple wishlist item collection filtered by wishlist item id
     *
     * @param int $id
     * @return array
     */
    protected function getItemList($id)
    {
        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);

        return $this->moduleHelper->getMultipleWishlistItems($multipleWishlist, $id);
    }

    /**
     * Changes core representation logic
     *
     * @param WishlistItem $subject
     * @param bool $result
     * @param Product $product
     * @return bool
     */
    public function afterRepresentProduct(WishlistItem $subject, bool $result, Product $product)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        if (!$result) {
            return $result;
        }

        $itemList = $this->getItemList($subject->getId());
        if (!count($itemList)) {
            return false;
        }

        return $result;
    }
}
