<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Block;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistSearchResultsInterface;
use BKozlic\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use BKozlic\MultipleWishlist\Helper\Data as BKozlicData;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Template;
use Magento\Wishlist\Helper\Data;
//@TODO Change item count in the my account dropdown
/**
 * Block class for multiple wishlist switcher rendering
 */
class MultipleWishlistSwitcher extends Template
{
    /**
     * @var MultipleWishlistRepositoryInterface
     */
    protected $multipleWishlistRepository;

    /**
     * @var MultipleWishlistSearchResultsInterface
     */
    protected $collection;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var Data
     */
    protected $wishlistHelper;

    /**
     * @var BKozlicData
     */
    protected $moduleHelper;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * MultipleWishlistSwitcher constructor.
     *
     * @param Template\Context $context
     * @param MultipleWishlistRepositoryInterface $multipleWishlistRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Data $wishlistHelper
     * @param BKozlicData $moduleHelper
     * @param RequestInterface $request
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        MultipleWishlistRepositoryInterface $multipleWishlistRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Data $wishlistHelper,
        BKozlicData $moduleHelper,
        RequestInterface $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->multipleWishlistRepository = $multipleWishlistRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->wishlistHelper = $wishlistHelper;
        $this->moduleHelper = $moduleHelper;
        $this->request = $request;
    }

    /**
     * Returns multiple wishlists
     *
     * @return MultipleWishlistSearchResultsInterface
     */
    public function getCollection()
    {
        if ($this->collection === null) {
            $wishlistId = $this->wishlistHelper->getWishlist()->getId();
            $this->searchCriteriaBuilder->addFilter(
                MultipleWishlistInterface::WISHLIST_ID,
                $wishlistId
            );


            $this->collection = $this->multipleWishlistRepository->getList(
                $this->searchCriteriaBuilder->create()
            )->getItems();
        }

        return $this->collection;
    }

    /**
     * Checks if there are the items in the default wishlist
     *
     * @return bool
     */
    public function defaultWishlistHasItems()
    {
        $items = $this->moduleHelper->getMultipleWishlistItems(null);

        return (bool)count($items);
    }

    /**
     * Checks if main wishlist is enabled
     *
     * @return bool
     */
    public function isWishlistAllowed()
    {
        return $this->wishlistHelper->isAllow();
    }

    /**
     * Checks if multiple wishlist module is enabled
     *
     * @return bool
     */
    public function isMultipleWishlistAllowed()
    {
        return $this->moduleHelper->isEnabled();
    }

    /**
     * Returns url with multiple wishlist param
     *
     * @param  int|null $multipleWishlistId
     * @return string
     */
    public function getMultipleWishlistUrl($multipleWishlistId)
    {
        $urlParams = [
            '_current' => true,
            '_escape' => true,
            '_fragment' => null,
            MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME => $multipleWishlistId
        ];

        return $this->getUrl('*/*/*', $urlParams);
    }

    /**
     * Checks if wishlist should be selected
     *
     * @param int|null $multipleWishlistId
     * @return bool
     */
    public function isSelectedWishlist($multipleWishlistId)
    {
        $wishlistRequestParam = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        return $wishlistRequestParam === $multipleWishlistId;
    }
}
