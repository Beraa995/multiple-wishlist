<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\ViewModel;

use BKozlic\MultipleWishlist\Api\Data\MultipleWishlistInterface;
use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
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
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * MultipleWishlist constructor.
     *
     * @param Session $customerSession
     * @param Data $moduleHelper
     * @param UrlInterface $urlBuilder
     * @param RequestInterface $request
     */
    public function __construct(
        Session $customerSession,
        Data $moduleHelper,
        UrlInterface $urlBuilder,
        RequestInterface $request
    ) {
        $this->customerSession = $customerSession;
        $this->moduleHelper = $moduleHelper;
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Checks if customer is logged in
     *
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

    /**
     * Returns form url for multiple wishlist create|edit
     *
     * @return string
     */
    public function getFormPostUrl()
    {
        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);

        if ($multipleWishlist) {
            return $this->urlBuilder->getUrl('multiplewishlist/manage/editpost');
        }

        return $this->urlBuilder->getUrl('multiplewishlist/manage/create');
    }

    /**
     * Returns wishlist name for the multiple wishlist manage form
     *
     * @return array
     */
    public function getManageFormInputValues()
    {
        $multipleWishlist = $this->request->getParam(MultipleWishlistInterface::MULTIPLE_WISHLIST_PARAM_NAME);
        $wishlist = $this->moduleHelper->getMultipleWishlist($multipleWishlist);

        if (!$wishlist) {
            return [];
        }

        return [
            'id' => $wishlist->getId(),
            'name' => $wishlist->getName()
        ];
    }
}
