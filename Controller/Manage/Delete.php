<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Controller\Manage;

use BKozlic\MultipleWishlist\Controller\AbstractManage;
use BKozlic\MultipleWishlist\Helper\Data;
use BKozlic\MultipleWishlist\Model\MultipleWishlistRepository;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;

/**
 * Controller for multiple wishlist deletion
 */
class Delete extends AbstractManage implements HttpPostActionInterface
{
    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * Delete constructor.
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param Validator $formKeyValidator
     * @param MultipleWishlistRepository $multipleWishlistRepository
     * @param Data $moduleHelper
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        Validator $formKeyValidator,
        MultipleWishlistRepository $multipleWishlistRepository,
        Data $moduleHelper
    ) {
        parent::__construct(
            $context,
            $urlBuilder,
            $formKeyValidator,
            $multipleWishlistRepository
        );
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Process multiple wishlist removal
     *
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        //@TODO Prevent saving wishlist with the same name
        $params = $this->getRequest()->getParams();

        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->processReturn(
                __('Invalid Form Key. Please refresh the page.'),
                false
            );
        }

        if (!isset($params['id'])) {
            return $this->processReturn(
                __('Required data missing!'),
                false
            );
        }

        try {
            $this->multipleWishlistRepository->deleteById($params['id']);
        } catch (CouldNotDeleteException $e) {
            return $this->processReturn(
                __('Something went wrong.'),
                false
            );
        } catch (NoSuchEntityException $e) {
            return $this->processReturn(
                __('Wishlist doesn\'t exist.'),
                false
            );
        } catch (LocalizedException $e) {
            return $this->processReturn(
                __('Something went wrong.'),
                false
            );
        }

        $this->moduleHelper->recalculateDefaultWishlistItems();

        return $this->processReturn(
            __('Wishlist has been successfully removed.'),
            true
        );
    }
}
