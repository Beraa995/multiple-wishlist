<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\CustomerData;

use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;

/**
 * Add new customer section
 */
class MultipleWishlist implements SectionSourceInterface
{
    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * MultipleWishlist constructor.
     * @param WishlistHelper $wishlistHelper
     * @param UrlInterface $urlBuilder
     * @param Data $moduleHelper
     */
    public function __construct(
        WishlistHelper $wishlistHelper,
        UrlInterface $urlBuilder,
        Data $moduleHelper
    ) {
        $this->wishlistHelper = $wishlistHelper;
        $this->urlBuilder = $urlBuilder;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        if (!$this->moduleHelper->isEnabled()) {
            return [];
        }

        return [
            'createUrl' => $this->urlBuilder->getUrl('multiplewishlist/manage/create', []),
            'items' => $this->getItems(),
        ];
    }

    /**
     * Returns multiple wishlists for the current main wishlist id
     * @return array
     */
    protected function getItems()
    {
        $collection = $this->moduleHelper->getAllMultipleWishlists($this->wishlistHelper->getWishlist()->getId());
        $items = [];

        foreach ($collection as $multipleWishlist) {
            $items[] = [
                'id' => $multipleWishlist->getId(),
                'name' => $multipleWishlist->getName()
            ];
        }

        return $items;
    }
}
