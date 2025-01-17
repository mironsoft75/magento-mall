<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\SpinToWin\Model;

use Magento\Framework\Model\AbstractModel;
use Webkul\SpinToWin\Api\Data\InfoInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * SpinToWin Info Model.
 *
 * @method \Webkul\SpinToWin\Model\ResourceModel\SpinToWin _getResource()
 * @method \Webkul\SpinToWin\Model\ResourceModel\SpinToWin getResource()
 */
class Info extends AbstractModel implements IdentityInterface, InfoInterface
{
    /**
     * No route page id.
     */
    public const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Marketplace SpinToWin cache tag.
     */
    public const CACHE_TAG = 'spintowin_info';

    /**
     * @var string
     */
    public $_cacheTag = 'spintowin_info';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    public $_eventPrefix = 'spintowin_info';

    /**
     * Constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param EditFormFactory $editFormFactory
     * @param ResultFormFactory $resultFormFactory
     * @param WheelFactory $wheelFactory
     * @param LayoutFactory $layoutFactory
     * @param VisibilityFactory $visibilityFactory
     * @param ButtonFactory $buttonFactory
     * @param CouponFactory $couponFactory
     * @param SegmentsFactory $segmentsFactory
     * @param ReportsFactory $reportsFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        EditFormFactory $editFormFactory,
        ResultFormFactory $resultFormFactory,
        WheelFactory $wheelFactory,
        LayoutFactory $layoutFactory,
        VisibilityFactory $visibilityFactory,
        ButtonFactory $buttonFactory,
        CouponFactory $couponFactory,
        SegmentsFactory $segmentsFactory,
        ReportsFactory $reportsFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->editFormFactory = $editFormFactory;
        $this->resultFormFactory = $resultFormFactory;
        $this->wheelFactory = $wheelFactory;
        $this->layoutFactory = $layoutFactory;
        $this->visibilityFactory = $visibilityFactory;
        $this->buttonFactory = $buttonFactory;
        $this->couponFactory = $couponFactory;
        $this->segmentsFactory = $segmentsFactory;
        $this->reportsFactory = $reportsFactory;
    }

    /**
     * Initialize resource model.
     */
    public function _construct()
    {
        $this->_init(\Webkul\SpinToWin\Model\ResourceModel\Info::class);
    }

    /**
     * Load object data.
     *
     * @param int|null $id
     * @param string   $field
     *
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteSpinToWin();
        }

        return parent::load($id, $field);
    }

    /**
     * Load No-Route SpinToWin.
     *
     * @return \Webkul\SpinToWin\Model\Info
     */
    public function noRouteSpinToWin()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Webkul\SpinToWin\Api\Data\InfoInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Get Edit Form
     */
    public function getEditForm()
    {
        return $this->editFormFactory->create()->load($this->getId(), 'spin_id');
    }
    /**
     * Get Result Form
     */
    public function getResultForm()
    {
        return $this->resultFormFactory->create()->load($this->getId(), 'spin_id');
    }

    /**
     * Get Wheel
     *
     * @return WheelFactory
     */
    public function getWheel()
    {
        return $this->wheelFactory->create()->load($this->getId(), 'spin_id');
    }

    /**
     * Get Layout
     *
     * @return LayoutFactory
     */
    public function getLayout()
    {
        return $this->layoutFactory->create()->load($this->getId(), 'spin_id');
    }

    /**
     * Get Visibility
     *
     * @return VisibilityFactory
     */
    public function getVisibility()
    {
        return $this->visibilityFactory->create()->load($this->getId(), 'spin_id');
    }

    /**
     * Get Button
     *
     * @return ButtonFactory
     */
    public function getButton()
    {
        return $this->buttonFactory->create()->load($this->getId(), 'spin_id');
    }

    /**
     * Get Coupon
     *
     * @return CouponFactory
     */
    public function getCoupon()
    {
        return $this->couponFactory->create()->load($this->getId(), 'spin_id');
    }

    /**
     * Get segments
     *
     * @return SegmentsFactory
     */
    public function getSegments()
    {
        return $this->segmentsFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('spin_id', $this->getId())->setOrder('position', 'ASC');
    }
    
    /**
     * Get Reports
     *
     * @return ReportsFactory
     */
    public function getReports()
    {
        return $this->reportsFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('spin_id', $this->getId());
    }
}
