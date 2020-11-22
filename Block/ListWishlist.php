<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Block;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Helper\Data as ModuleData;
use BKozlic\MultipleWishlist\Model\ResourceModel\MultipleWishlist\Collection;
use BKozlic\MultipleWishlist\Model\ResourceModel\MultipleWishlist\CollectionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Template;
use Magento\Wishlist\Helper\Data;

/**
 * Block class for multiple wishlist list rendering
 */
class ListWishlist extends Template
{
    /**
     * @var Data
     */
    protected $wishlistHelper;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var ModuleData
     */
    protected $moduleHelper;

    /**
     * ListWishlist constructor.
     *
     * @param Template\Context $context
     * @param Data $wishlistHelper
     * @param CollectionFactory $collectionFactory
     * @param ModuleData $moduleHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $wishlistHelper,
        CollectionFactory $collectionFactory,
        ModuleData $moduleHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->wishlistHelper = $wishlistHelper;
        $this->collectionFactory = $collectionFactory;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Prepares the multiple wishlist collection
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $block = $this->getChildBlock('multiple.wishlist.pager');

        if ($block instanceof DataObject) {
            $block->setCollection(
                $this->getCollection()
            );
        }
    }

    /**
     * Returns number of product in the wishlist
     *
     * @param $wishlistId
     * @return int
     */
    public function countItems($wishlistId)
    {
        return $this->moduleHelper->countMultipleWishlistItems($wishlistId);
    }

    /**
     * Returns multiple wishlists
     *
     * @return Collection
     */
    public function getCollection()
    {
        if ($this->collection === null) {
            $wishlistId = $this->wishlistHelper->getWishlist()->getId();
            $this->collection = $this->collectionFactory->create();
            $this->collection->addFieldToFilter(MultipleWishlistInterface::WISHLIST_ID, $wishlistId)
                ->setOrder(MultipleWishlistInterface::MULTIPLE_WISHLIST_ID, 'desc');
        }

        return $this->collection;
    }
}
