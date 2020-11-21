<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Plugin\Controller\Shared;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Wishlist\Controller\Shared\WishlistProvider as SharedProvider;
use Magento\Wishlist\Model\ResourceModel\Wishlist as WishlistResource;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory;
use Psr\Log\LoggerInterface;

/**
 * Plugin class for loading correct multiple wishlist by given code
 */
class WishlistProvider
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
     * @var MultipleWishlistRepositoryInterface
     */
    protected $multipleWishlistRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var WishlistResource
     */
    protected $wishlistResource;

    /**
     * Wishlist Index Controller Plugin constructor.
     *
     * @param RequestInterface $request
     * @param MultipleWishlistRepositoryInterface $multipleWishlistRepository
     * @param Data $moduleHelper
     * @param LoggerInterface $logger
     * @param WishlistFactory $wishlistFactory
     * @param WishlistResource $wishlistResource
     */
    public function __construct(
        RequestInterface $request,
        MultipleWishlistRepositoryInterface $multipleWishlistRepository,
        Data $moduleHelper,
        LoggerInterface $logger,
        WishlistFactory $wishlistFactory,
        WishlistResource $wishlistResource
    ) {
        $this->request = $request;
        $this->moduleHelper = $moduleHelper;
        $this->multipleWishlistRepository = $multipleWishlistRepository;
        $this->logger = $logger;
        $this->wishlistFactory = $wishlistFactory;
        $this->wishlistResource = $wishlistResource;
    }

    /**
     * Return correct multiple wishlist by given code
     *
     * @param SharedProvider $subject
     * @param Wishlist|bool $result
     * @return Wishlist|bool
     */
    public function afterGetWishlist(SharedProvider $subject, $result)
    {
        //@TODO Show wishlist name
        if (!$this->moduleHelper->isEnabled()) {
            return $result;
        }

        $code = (string)$this->request->getParam('code');
        $multipleWishlist = null;
        try {
            $multipleWishlist = $this->multipleWishlistRepository->getByCode($code);
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
        }

        if ($multipleWishlist && $multipleWishlist->getId()) {
            $wishlist = $this->wishlistFactory->create();
            $this->wishlistResource->load($wishlist, $multipleWishlist->getWishlistId());

            if (!$wishlist->getId()) {
                return $result;
            }

            $wishlist->setTempSharingCode($wishlist->getSharingCode());
            $wishlist->setSharingCode($multipleWishlist->getSharingCode());
            $this->request->setParams(array_merge(
                $this->request->getParams(),
                [MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME => $multipleWishlist->getId()]
            ));

            return $wishlist;
        }

        return $result;
    }
}
