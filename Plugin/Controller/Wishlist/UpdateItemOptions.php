<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Plugin\Controller\Wishlist;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Controller\Index\UpdateItemOptions as MagentoWishlistController;
use Magento\Wishlist\Controller\WishlistProviderInterface;

/**
 * Plugin class for redirecting to the shared wishlist after share
 */
class UpdateItemOptions
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
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * Wishlist Send Controller Plugin constructor.
     *
     * @param RequestInterface $request
     * @param Data $moduleHelper
     * @param ResultFactory $resultFactory
     * @param ManagerInterface $messageManager
     * @param UrlInterface $urlBuilder
     * @param WishlistProviderInterface $wishlistProvider
     */
    public function __construct(
        RequestInterface $request,
        Data $moduleHelper,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        UrlInterface $urlBuilder,
        WishlistProviderInterface $wishlistProvider
    ) {
        $this->request = $request;
        $this->moduleHelper = $moduleHelper;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->urlBuilder = $urlBuilder;
        $this->wishlistProvider = $wishlistProvider;
    }

    /**
     * Redirect to the right wishlist after configure
     *
     * @param MagentoWishlistController $subject
     * @param Redirect $result
     * @return Redirect
     */
    public function afterExecute(MagentoWishlistController $subject, $result)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $wishlist = $this->wishlistProvider->getWishlist();
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        $params = [
            'wishlist_id' => $wishlist->getId()
        ];

        if ($multipleWishlist) {
            $params[MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME] = $multipleWishlist;
        }

        $url = $this->urlBuilder->getUrl('*/*', $params);
        $successMessages = $this->messageManager->getMessages()->getItemsByType(MessageInterface::TYPE_SUCCESS);
        if (count($successMessages)) {
            return $resultRedirect->setUrl($url);
        }

        return $result;
    }
}
