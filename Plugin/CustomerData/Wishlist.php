<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Plugin\CustomerData;

use BKozlic\MultipleWishlist\Helper\Data as ModuleHelper;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Wishlist\Block\Customer\Sidebar;
use Magento\Wishlist\CustomerData\Wishlist as WishlistCustomerData;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;

/**
 * Plugin class for changing original wishlist customer data
 */
class Wishlist extends WishlistCustomerData
{
    /**
     * @var ModuleHelper
     */
    protected $moduleHelper;

    /**
     * @var Data
     */
    protected $wishlistHelper;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var CollectionFactory
     */
    protected $wishlistCollectionFactory;

    /**
     * @var ItemResolverInterface
     */
    protected $itemResolver;

    /**
     * Wishlist Customer Data Plugin constructor.
     *
     * @param Data $wishlistHelper
     * @param Sidebar $block
     * @param ImageFactory $imageHelperFactory
     * @param ViewInterface $view
     * @param ModuleHelper $moduleHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $wishlistCollectionFactory
     * @param ItemResolverInterface $itemResolver
     */
    public function __construct(
        Data $wishlistHelper,
        Sidebar $block,
        ImageFactory $imageHelperFactory,
        ViewInterface $view,
        ModuleHelper $moduleHelper,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $wishlistCollectionFactory,
        ItemResolverInterface $itemResolver
    ){
        parent::__construct($wishlistHelper, $block, $imageHelperFactory, $view);
        $this->moduleHelper = $moduleHelper;
        $this->scopeConfig = $scopeConfig;
        $this->wishlistCollectionFactory = $wishlistCollectionFactory;
        $this->itemResolver = $itemResolver;
    }

    /**
     * Changes original wishlist customer data
     *
     * @param WishlistCustomerData $subject
     * @param array $result
     * @throws NoSuchEntityException
     * @return array
     */
    public function afterGetSectionData(WishlistCustomerData $subject, array $result)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $items = $this->moduleHelper->getMultipleWishlistItems();
        $collection = $this->moduleHelper->getItemCollectionByItemIds($items);
        if ($this->scopeConfig->getValue(
            Data::XML_PATH_WISHLIST_LINK_USE_QTY,
            ScopeInterface::SCOPE_STORE
        )) {
            $itemCount = $collection->getItemsQty();
        } else {
            $itemCount = $collection->count();
        }

        $result['counter'] = $this->createCounter($itemCount);
        $result['items'] = $this->getSidebarItems($collection);

        return $result;
    }

    /**
     * Return items data for the sidebar
     *
     * @param $wishlistItemCollection
     * @return array
     */
    protected function getSidebarItems($wishlistItemCollection)
    {
        $wishlistItemCollection->clear()->setPageSize(self::SIDEBAR_ITEMS_NUMBER)
            ->setInStockFilter(true)->setOrder('added_at');

        $items = [];
        foreach ($wishlistItemCollection as $wishlistItem) {
            $items[] = $this->getItemData($wishlistItem);
        }

        return $items;
    }
}
