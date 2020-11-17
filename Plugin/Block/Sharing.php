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
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Block\Customer\Sharing as MagentoSharingBlock;

/**
 * Plugin class for adding multiple wishlist id to the send url
 */
class Sharing
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * Sharing Block Plugin constructor.
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param Data $moduleHelper
     */
    public function __construct(
        RequestInterface $request,
        UrlInterface $urlBuilder,
        Data $moduleHelper
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Add multiple wishlist param to the send url
     *
     * @param MagentoSharingBlock $subject
     * @param $result
     * @return string
     */
    public function afterGetSendUrl(MagentoSharingBlock $subject, $result)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);

        return $this->urlBuilder->getUrl('wishlist/index/send', [
            MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME => $multipleWishlist
        ]);
    }
}
