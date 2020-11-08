<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;

class MultipleWishlist implements SectionSourceInterface
{
    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * MultipleWishlist constructor.
     * @param WishlistHelper $wishlistHelper
     */
    public function __construct(
        WishlistHelper $wishlistHelper
    ) {
        $this->wishlistHelper = $wishlistHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        return [
            'wishlistId' => $this->wishlistHelper->getWishlist()->getId(),
        ];
    }
}
