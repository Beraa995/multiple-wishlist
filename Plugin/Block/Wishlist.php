<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Plugin\Block;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Wishlist\Block\Customer\Wishlist as MagentoWishlistBlock;

/**
 * Plugin class for changing add all to cart route
 */
class Wishlist
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * @var Json
     */
    protected $json;

    /**
     * Wishlist Block Plugin constructor.
     *
     * @param RequestInterface $request
     * @param Data $moduleHelper
     * @param Json $json
     */
    public function __construct(
        RequestInterface $request,
        Data $moduleHelper,
        Json $json
    ) {
        $this->request = $request;
        $this->moduleHelper = $moduleHelper;
        $this->json = $json;
    }

    /**
     * Changes add all to cart route
     *
     * @param MagentoWishlistBlock $subject
     * @param string $result
     * @return string
     */
    public function afterGetAddAllToCartParams(MagentoWishlistBlock $subject, string $result)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        $paramsArray = $this->json->unserialize($result);

        if ($multipleWishlist) {
            $paramsArray['data'][MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME] = $multipleWishlist;
        }

        return $this->json->serialize($paramsArray);
    }
}
