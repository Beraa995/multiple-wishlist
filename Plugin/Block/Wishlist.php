<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Plugin\Block;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistItemInterface;
use BKozlic\MultipleWishlist\Api\MultipleWishlistItemRepositoryInterface;
use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Block\Customer\Wishlist as MagentoWishlistBlock;
use Magento\Wishlist\Model\ResourceModel\Item\Collection;

/**
 * Plugin class for filtering wishlist collection by current multiple wishlist id
 */
class Wishlist
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var MultipleWishlistItemRepositoryInterface
     */
    protected $itemRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Wishlist Block Plugin constructor.
     *
     * @param RequestInterface $request
     * @param MultipleWishlistItemRepositoryInterface $itemRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Data $moduleHelper
     * @param Json $json
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        RequestInterface $request,
        MultipleWishlistItemRepositoryInterface $itemRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Data $moduleHelper,
        Json $json,
        UrlInterface $urlBuilder
    ) {
        $this->request = $request;
        $this->itemRepository = $itemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->moduleHelper = $moduleHelper;
        $this->json = $json;
        $this->urlBuilder = $urlBuilder;
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

    /**
     * Process filtering collection by custom multiple wishlist id
     *
     * @param MagentoWishlistBlock $subject
     * @param Collection $result
     * @return Collection
     */
    public function afterGetWishlistItems(MagentoWishlistBlock $subject, Collection $result)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $multipleWishlistId = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        $idToQtyMapper = $this->getMultipleWishlistItemIdToQtyMapper($multipleWishlistId);

        $result->addFieldToFilter('wishlist_item_id', ['in' => array_keys($idToQtyMapper)]);

        foreach ($result as $wishlistItem) {
            if (isset($idToQtyMapper[$wishlistItem->getId()])) {
                $wishlistItem->setQty($idToQtyMapper[$wishlistItem->getId()]['qty']);
                $wishlistItem->setDescription($idToQtyMapper[$wishlistItem->getId()]['desc']);
            }
        }

        return $result;
    }

    /**
     * Returns ids of items in the specified multiple wishlist with description and qty
     *
     * @param int|null $multipleWishlistId
     * @return array
     */
    protected function getMultipleWishlistItemIdToQtyMapper($multipleWishlistId)
    {
        //@TODO If there are no items to the default wishlist use first one from the list
        $itemList = $this->moduleHelper->getMultipleWishlistItems($multipleWishlistId);
        $ids = [];
        foreach ($itemList as $item) {
            $ids[$item->getWishlistItemId()]['qty'] = $item->getQty();
            $ids[$item->getWishlistItemId()]['desc'] = $item->getDescription();
        }

        return $ids;
    }
}
