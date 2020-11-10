<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\CustomerData;

use BKozlic\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;

/**
 * Add new customer section
 */
class MultipleWishlist implements SectionSourceInterface
{
    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var MultipleWishlistRepositoryInterface
     */
    protected $multipleWishlistRepository;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * MultipleWishlist constructor.
     * @param WishlistHelper $wishlistHelper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param MultipleWishlistRepositoryInterface $multipleWishlistRepository
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        WishlistHelper $wishlistHelper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        MultipleWishlistRepositoryInterface $multipleWishlistRepository,
        UrlInterface $urlBuilder
    ) {
        $this->wishlistHelper = $wishlistHelper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->multipleWishlistRepository = $multipleWishlistRepository;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        return [
            'createUrl' => $this->urlBuilder->getUrl('multiplewishlist/manage/create', []),
            'items' => $this->getItems(),
        ];
    }

    /**
     * Returns multiple wishlists for the current main wishlist id
     * @return array
     */
    protected function getItems()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('wishlist_id', $this->wishlistHelper->getWishlist()->getId(), 'eq')
            ->create();

        $collection = $this->multipleWishlistRepository->getList($searchCriteria)->getItems();
        $items = [];

        foreach ($collection as $multipleWishlist) {
            $items[] = [
                'id' => $multipleWishlist->getId(),
                'name' => $multipleWishlist->getName()
            ];
        }

        return $items;
    }
}
