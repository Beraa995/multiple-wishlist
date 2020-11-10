<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Plugin\Block;

use Magento\Wishlist\Block\Customer\Wishlist as MagentoWishlistBlock;
use Magento\Wishlist\Model\ResourceModel\Item\Collection;

/**
 * Plugin class for filtering wishlist collection by current multiple wishlist id
 */
class Wishlist
{
    /**
     * Process filtering collection by custom multiple wishlist id
     *
     * @param MagentoWishlistBlock $subject
     * @param Collection $result
     * @return Collection
     */
    public function afterGetWishlistItems(MagentoWishlistBlock $subject, Collection $result)
    {
        return $result;
    }
}
