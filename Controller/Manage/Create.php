<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Controller\Manage;

use BKozlic\MultipleWishlist\Model\MultipleWishlistFactory;
use BKozlic\MultipleWishlist\Model\MultipleWishlistRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\CouldNotSaveException;

class Create extends Action implements HttpPostActionInterface
{
    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var MultipleWishlistFactory
     */
    protected $multipleWishlistFactory;

    /**
     * @var MultipleWishlistRepository
     */
    protected $multipleWishlistRepository;

    /**
     * Create constructor.
     * @param Context $context
     * @param Validator $formKeyValidator
     * @param MultipleWishlistFactory $multipleWishlistFactory
     * @param MultipleWishlistRepository $multipleWishlistRepository
     */
    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        MultipleWishlistFactory $multipleWishlistFactory,
        MultipleWishlistRepository $multipleWishlistRepository
    ) {
        parent::__construct($context);
        $this->formKeyValidator = $formKeyValidator;
        $this->multipleWishlistFactory = $multipleWishlistFactory;
        $this->multipleWishlistRepository = $multipleWishlistRepository;
    }

    /**
     * Process multiple wishlist creation
     */
    public function execute()
    {
        //@TODO Check ajax request
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        $params = $this->getRequest()->getParams();
        if (!isset($params['wishlist']) || !isset($params['name'])) {
            $this->messageManager->addErrorMessage(__('Required data missing!'));
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        $multipleWishlist = $this->multipleWishlistFactory->create();
        $multipleWishlist->setWishlistId($params['wishlist']);
        $multipleWishlist->setName($params['name']);

        try {
            $this->multipleWishlistRepository->save($multipleWishlist);
        } catch (CouldNotSaveException $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the wishlist!'));
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        $this->messageManager->addSuccessMessage(__('Wishlist has been successfully saved!'));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    }
}
