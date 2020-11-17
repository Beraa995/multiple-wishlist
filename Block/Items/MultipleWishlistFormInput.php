<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Block\Items;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use Magento\Wishlist\Block\Customer\Wishlist\Item\Column;

class MultipleWishlistFormInput extends Column
{
    /**
     * Returns multiple wishlist input for the wishlist form
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        $paramName = MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME;
        $multipleWishlistId = $this->getRequest()->getParam($paramName);

        return '<input type="hidden" name="' . $paramName . '" value="' . $multipleWishlistId . '">';
    }
}
