<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Plugin\CustomerData;

use BKozlic\MultipleWishlist\Helper\Data as ModuleHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Wishlist\CustomerData\Wishlist as WishlistCustomerData;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;

/**
 * Plugin class for changing original wishlist customer data
 */
class Wishlist
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
     * Wishlist Customer Data Plugin constructor.
     *
     * @param ModuleHelper $moduleHelper
     * @param Data $wishlistHelper
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $wishlistCollectionFactory
     */
    public function __construct(
        ModuleHelper $moduleHelper,
        Data $wishlistHelper,
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $wishlistCollectionFactory
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->wishlistHelper = $wishlistHelper;
        $this->scopeConfig = $scopeConfig;
        $this->wishlistCollectionFactory = $wishlistCollectionFactory;
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
        //@TODO Check the error when module is disabled after being used
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

        $result['counter'] = $itemCount;

        return $result;
    }
}
