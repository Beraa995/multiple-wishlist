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
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Controller\Index\Cart as MagentoWishlistController;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\ResourceModel\Item;

/**
 * Plugin class for adding multiple wishlist param to the configure url
 */
class Cart
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
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * @var Item
     */
    protected $itemResource;

    /**
     * Wishlist Index Controller Plugin constructor.
     *
     * @param RequestInterface $request
     * @param Data $moduleHelper
     * @param ResultFactory $resultFactory
     * @param ManagerInterface $messageManager
     * @param UrlInterface $urlBuilder
     * @param ItemFactory $itemFactory
     * @param Item $itemResource
     */
    public function __construct(
        RequestInterface $request,
        Data $moduleHelper,
        ResultFactory $resultFactory,
        ManagerInterface $messageManager,
        UrlInterface $urlBuilder,
        ItemFactory $itemFactory,
        Item $itemResource
    ) {
        $this->request = $request;
        $this->moduleHelper = $moduleHelper;
        $this->resultFactory = $resultFactory;
        $this->messageManager = $messageManager;
        $this->urlBuilder = $urlBuilder;
        $this->itemFactory = $itemFactory;
        $this->itemResource = $itemResource;
    }

    /**
     * Change configure url redirect
     *
     * @param MagentoWishlistController $subject
     * @param Redirect|Json $result
     * @return Redirect|Json
     */
    public function afterExecute(MagentoWishlistController $subject, $result)
    {
        //@TODO Clear all messages and add messages in the plugin(s).
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        $itemId = $this->request->getParam('item');
        $item = $this->itemFactory->create();
        $this->itemResource->load($item, $itemId);

        if (!$item->getId()) {
            return $result;
        }

        $params = [
            'id' => $item->getId(),
            'product_id' => $item->getProductId(),
        ];

        if ($multipleWishlist) {
            $params[MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME] = $multipleWishlist;
        }

        $url = $this->urlBuilder->getUrl('*/*/configure/', $params);
        $noticeMessages = $this->messageManager->getMessages()->getItemsByType(MessageInterface::TYPE_NOTICE);
        if (count($noticeMessages)) {
            //@TODO Add logic for ajax
            if ($subject->getRequest()->isAjax()) {
                return $result;
            }

            return $resultRedirect->setUrl($url);
        }

        return $result;
    }
}
