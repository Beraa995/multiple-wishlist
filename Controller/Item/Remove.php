<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Controller\Item;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistItemInterface;
use BKozlic\MultipleWishlist\Api\MultipleWishlistItemRepositoryInterface;
use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\CouldNotDeleteException;
use Psr\Log\LoggerInterface;

/**
 * Controller for multiple wishlist item removal
 */
class Remove extends Action implements HttpPostActionInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

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
     * Remove item controller constructor.
     * @param Context $context
     * @param LoggerInterface $logger
     * @param MultipleWishlistItemRepositoryInterface $itemRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Data $moduleHelper
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        MultipleWishlistItemRepositoryInterface $itemRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Data $moduleHelper,
        Validator $formKeyValidator
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->formKeyValidator = $formKeyValidator;
        $this->itemRepository = $itemRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Process multiple wishlist item removal
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        $mainItemId = $this->getRequest()->getParam('item');
        $multipleWishlist = $this->getRequest()->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        $items = $this->moduleHelper->getMultipleWishlistItems($multipleWishlist, $mainItemId);

        foreach ($items as $item) {
            try {
                $this->itemRepository->delete($item);
                $this->moduleHelper->recalculate($item->getWishlistItemId());
                $this->messageManager->addSuccessMessage(__('Item has been removed from your Wish List.'));
            } catch (CouldNotDeleteException $e) {
                $this->logger->error($e->getMessage());
                $this->messageManager->addErrorMessage(
                    __('We can\'t delete the item from the Wish List right now.')
                );
            }
        }

        return $resultRedirect->setPath($this->_redirect->getRefererUrl());
    }
}
