<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Plugin\Helper\Wishlist;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Helper\Data as ModuleHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Wishlist\Helper\Data as WishlistHelper;

/**
 * Plugin class for changing add to cart params in the url
 */
class Data
{
    /**
     * @var ModuleHelper
     */
    protected $moduleHelper;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Helper Plugin constructor.
     * @param ModuleHelper $moduleHelper
     * @param RequestInterface $request
     * @param Json $json
     */
    public function __construct(
        ModuleHelper $moduleHelper,
        RequestInterface $request,
        Json $json
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->json = $json;
        $this->request = $request;
    }

    /**
     * Add multiple wishlist id to the add to cart params
     *
     * @param WishlistHelper $subject
     * @param $result
     * @param $item
     * @param false $addReferer
     * @return string
     */
    public function afterGetAddToCartParams(WishlistHelper $subject, $result, $item, $addReferer = false)
    {
        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        $params = $this->json->unserialize($result);
        if ($multipleWishlist) {
            $params['data'][MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME] = $multipleWishlist;
        }

        return $this->json->serialize($params);
    }
}
