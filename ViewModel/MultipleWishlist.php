<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\ViewModel;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Api\MultipleWishlistRepositoryInterface;
use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Module's view model class
 */
class MultipleWishlist implements ArgumentInterface
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * MultipleWishlist constructor.
     * @param Session $customerSession
     * @param Data $moduleHelper
     */
    public function __construct(
        Session $customerSession,
        Data $moduleHelper
    ) {
        $this->customerSession = $customerSession;
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Checks if customer is logged in
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->customerSession->getCustomerGroupId() !== Group::NOT_LOGGED_IN_ID;
    }

    /**
     * Checks if modal can be shown
     *
     * @return bool
     */
    public function canShowModal()
    {
        return $this->moduleHelper->canShowModal();
    }
}
