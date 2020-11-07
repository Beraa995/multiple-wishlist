<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Controller\Manage;

use BKozlic\MultipleWishlist\Model\MultipleWishlistRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Delete extends Action implements HttpPostActionInterface
{
    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var MultipleWishlistRepository
     */
    protected $multipleWishlistRepository;

    /**
     * Create constructor.
     * @param Context $context
     * @param Validator $formKeyValidator
     * @param MultipleWishlistRepository $multipleWishlistRepository
     */
    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        MultipleWishlistRepository $multipleWishlistRepository
    ) {
        parent::__construct($context);
        $this->formKeyValidator = $formKeyValidator;
        $this->multipleWishlistRepository = $multipleWishlistRepository;
    }

    /**
     * Process multiple wishlist removal
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
        if (!isset($params['id'])) {
            $this->messageManager->addErrorMessage(__('Required data missing!'));
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        try {
            $this->multipleWishlistRepository->deleteById($params['id']);
        } catch (CouldNotDeleteException $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while removing the wishlist!'));
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__('Wishlist doesn\'t exist!'));
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong!'));
            return $resultRedirect->setPath($this->_redirect->getRefererUrl());
        }

        $this->messageManager->addSuccessMessage(__('Wishlist has been successfully removed!'));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    }
}
