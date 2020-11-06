<?php
/**
 * @category  BKozlic
 * @package   BKozlic\MultipleWishlist
 * @author    Berin Kozlic - berin.kozlic@gmail.com
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
namespace BKozlic\MultipleWishlist\Model;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

class MultipleWishlist extends AbstractExtensibleModel implements IdentityInterface, ExtensibleDataInterface
{
    const CACHE_TAG = 'multiple_wishlist';

    /**
     * @var string
     */
    protected $_cacheTag = 'multiple_wishlist';

    /**
     * @var string
     */
    protected $_eventPrefix = 'multiple_wishlist';

    /**
     * @inheridoc
     */
    public function _construct()
    {
        $this->_init(ResourceModel\MultipleWishlist::class);
    }

    /**
     * @inheridoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
