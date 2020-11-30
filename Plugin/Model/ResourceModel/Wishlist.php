<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Plugin\Model\ResourceModel;

use BKozlic\MultipleWishlist\Helper\Data;
use Magento\Framework\Model\AbstractModel;
use Magento\Wishlist\Model\ResourceModel\Wishlist as MagentoWishlistResource;

/**
 * Class for changing sharing code before save
 */
class Wishlist
{
    /**
     * @var Data
     */
    protected $moduleHelper;

    /**
     * Wishlist Resource Model Plugin constructor.
     *
     * @param Data $moduleHelper
     */
    public function __construct(Data $moduleHelper)
    {
        $this->moduleHelper = $moduleHelper;
    }

    /**
     * Handle saving of the sharing code
     *
     * @param MagentoWishlistResource $subject
     * @param AbstractModel $object
     * @return array
     */
    public function beforeSave(MagentoWishlistResource $subject, AbstractModel $object)
    {
        if (!$this->moduleHelper->isEnabled()) {
            return [$object];
        }

        if ($code = $object->getTempSharingCode()) {
            $object->setSharingCode($code);
        }

        return [$object];
    }
}
