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
use Magento\Framework\UrlInterface;
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
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Helper Plugin constructor.
     * @param ModuleHelper $moduleHelper
     * @param RequestInterface $request
     * @param Json $json
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ModuleHelper $moduleHelper,
        RequestInterface $request,
        Json $json,
        UrlInterface $urlBuilder
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->json = $json;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
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
        //@TODO Check sharedAddAllToCart from the helper
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $params = $this->addWishlistParam($result);

        return $this->json->serialize($params);
    }

    /**
     * Add multiple wishlist id to the update url params
     *
     * @param WishlistHelper $subject
     * @param $result
     * @param $item
     * @return string
     */
    public function afterGetUpdateParams(WishlistHelper $subject, $result, $item)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        //@TODO Prevent removal of not configured products in other wishlists which are same as the current.
        $params = $this->addWishlistParam($result);

        return $this->json->serialize($params);
    }

    /**
     * Add multiple wishlist id to the delete params and change url
     *
     * @param WishlistHelper $subject
     * @param $result
     * @param $item
     * @param false $addReferer
     * @return mixed
     */
    public function afterGetRemoveParams(WishlistHelper $subject, $result, $item, $addReferer = false)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $params = $this->addWishlistParam($result);
        $params['action'] = $this->urlBuilder->getUrl('multiplewishlist/item/remove', []);

        return $this->json->serialize($params);
    }

    /**
     * Add multiple wishlist id to the configure url params
     *
     * @param WishlistHelper $subject
     * @param $result
     * @param $item
     * @return string
     */
    public function afterGetConfigureUrl(WishlistHelper $subject, $result, $item)
    {
        //@TODO Provide correct qty
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        if ($multipleWishlist) {
            $result = $this->urlBuilder->getUrl(
                'wishlist/index/configure',
                [
                    'id' => $item->getWishlistItemId(),
                    'product_id' => $item->getProductId(),
                    MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME => $multipleWishlist
                ]
            );
        }

        return $result;
    }

    /**
     * Adds param to the array
     *
     * @param $params
     * @return array
     */
    protected function addWishlistParam($params)
    {
        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        $paramsArray = $this->json->unserialize($params);
        if ($multipleWishlist) {
            $paramsArray['data'][MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME] = $multipleWishlist;
        }

        return $paramsArray;
    }
}
