<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Controller\Manage;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Api\MultipleWishlistItemRepositoryInterface;
use BKozlic\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use BKozlic\MultipleWishlist\Controller\AbstractManage;
use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;
use Psr\Log\LoggerInterface;

/**
 * Controller for moving items to different wishlists
 */
class Move extends AbstractManage implements HttpPostActionInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * @var WishlistHelper
     */
    protected $wishlistHelper;

    /**
     * @var MultipleWishlistItemRepositoryInterface
     */
    protected $multipleWishlistItemRepository;

    /**
     * Move constructor.
     *
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param MultipleWishlistRepositoryInterface $multipleWishlistRepository
     * @param LoggerInterface $logger
     * @param Validator $formKeyValidator
     * @param WishlistHelper $wishlistHelper
     * @param Data $moduleHelper
     * @param MultipleWishlistItemRepositoryInterface $multipleWishlistItemRepository
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        MultipleWishlistRepositoryInterface $multipleWishlistRepository,
        LoggerInterface $logger,
        Validator $formKeyValidator,
        WishlistHelper $wishlistHelper,
        Data $moduleHelper,
        MultipleWishlistItemRepositoryInterface $multipleWishlistItemRepository
    ) {
        parent::__construct(
            $context,
            $urlBuilder,
            $formKeyValidator,
            $multipleWishlistRepository
        );
        $this->logger = $logger;
        $this->moduleHelper = $moduleHelper;
        $this->wishlistHelper = $wishlistHelper;
        $this->multipleWishlistItemRepository = $multipleWishlistItemRepository;
    }

    /**
     * Move all items from one wishlist to another
     *
     * @param int $prevWishlist
     * @param int $newWishlist
     * @return bool
     */
    protected function moveAllItems($prevWishlist, $newWishlist)
    {
        $items = $this->moduleHelper->getMultipleWishlistItems($prevWishlist);
        foreach ($items as $item) {
            $item->setMultipleWishlistId($newWishlist);
            try {
                $this->multipleWishlistItemRepository->save($item);
            } catch (CouldNotSaveException $e) {
                $this->logger->error($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Move one item to the different wishlist
     *
     * @param int $newWishlist
     * @param int $itemId
     * @return bool
     */
    protected function moveItem($newWishlist, $itemId)
    {
        try {
            $item = $this->multipleWishlistItemRepository->getByWishlistItemId($itemId);
            $item->setMultipleWishlistId($newWishlist);
            $this->multipleWishlistItemRepository->save($item);
        } catch (CouldNotSaveException $e) {
            $this->logger->error($e->getMessage());
            return false;
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Process multiple wishlist item(s) move
     *
     * @throws NoSuchEntityException
     * @return Json|Redirect
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $wishlistId = $this->wishlistHelper->getWishlist()->getId();

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->processReturn(
                __('Invalid Form Key. Please refresh the page.'),
                false
            );
        }

        if (!$wishlistId) {
            return $this->processReturn(
                __('Something went wrong.'),
                false
            );
        }

        $multipleWishlist = $params[MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME] ?? null;
        $previousWishlist = $params['prev'] ?? null;
        if ($multipleWishlist === $previousWishlist) {
            return $this->processReturn(
                __('Can\'t move items to the same wishlist.'),
                false
            );
        }

        if (isset($params['item_id'])) {
            $result = $this->moveItem($multipleWishlist, $params['item_id']);
        } else {
            $result = $this->moveAllItems($previousWishlist, $multipleWishlist);
        }

        if (!$result) {
            return $this->processReturn(
                __('Something went wrong.'),
                false
            );
        }

        $this->moduleHelper->recalculateWishlistItems($multipleWishlist);

        return $this->processReturn(
            __('Items have been successfully moved.'),
            true,
            $this->urlBuilder->getUrl('wishlist', [
                MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME => $multipleWishlist
            ])
        );
    }
}
