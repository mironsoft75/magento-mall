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

namespace Webkul\SpinToWin\Block\Adminhtml;

class Report extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Webkul\SpinToWin\Model\ReportsFactory $reportsFactory
     * @param \Magento\Framework\Pricing\Helper\Data $pricing
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Webkul\SpinToWin\Model\ReportsFactory $reportsFactory,
        \Magento\Framework\Pricing\Helper\Data $pricing,
        array $data = []
    ) {
        $this->reportsFactory = $reportsFactory;
        $this->pricing = $pricing;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('spintowin_report_grid');
        $this->setDefaultSort('position', 'asc');
        $this->setUseAjax(true);
    }

    /**
     * Apply various selection filters to prepare the sales order grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $spinId = $this->getRequest()->getParam('spin_id');
        $collection = $this->reportsFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('main_table.spin_id', $spinId);
        $select = $collection->getSelectSql();
        $select->join(['segments' => $collection->getResource()->getTable('spintowin_segments')],'main_table.segment_id = segments.entity_id',['label']);  
        $collection->addFilterToMap('entity_id','main_table.entity_id');          
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

   /**
    * Prepare Columns
    *
    * @return void
    */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Id'),
                'index' => 'entity_id'
            ]
        );
        $this->addColumn(
            'label',
            [
                'header' => __('Label'),
                'index' => 'label',
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
            ]
        );
        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'index' => 'email',
            ]
        );
        $this->addColumn(
            'timestamp',
            [
                'header' => __('Spin Time'),
                'index' => 'timestamp',
            ]
        );
        $this->addColumn(
            'result',
            [
                'header' => __('Result'),
                'index' => 'result',
                'type' => 'options',
                'options' => [
                    0 => __('Lose'),
                    1 => __('Win'),
                ],

            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => [
                    0 => __('N/A'),
                    1 => __('To Redeem'),
                    2 => __('Redeemed'),
                ],

            ]
        );

        $this->addColumn(
            'spin_type',
            [
                'header' => __('Spin Type'),
                'index' => 'spin_type',
                'type' => 'options',
                'options' => [
                    0 => __('Lose'),
                    1 => __('Coupon'),
                    2 => __('Product'),
                    3 => __('Point'),
                    4 => __('Cloud Server')
                ]

            ]
        );
        $this->addColumn(
            'coupon',
            [
                'header' => __('Coupon'),
                'index' => 'coupon',
            ]
        );
        $this->addColumn(
            'segment_product_sku',
            [
                'header' => __('Product Sku'),
                'index' => 'segment_product_sku',
            ]
        );
        $this->addColumn(
            'segment_point',
            [
                'header' => __('Point'),
                'index' => 'segment_point',
            ]
        );
        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportExcel', __('Excel XML'));
        return parent::_prepareColumns();
    }

    /**
     * Get Total Spins
     *
     * @return \Webkul\SpinToWin\Model\ReportsFactory
     */
    public function getTotalSpins()
    {
        $spinId = $this->getRequest()->getParam('spin_id');
        $collection = $this->reportsFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('spin_id', $spinId);
        return $collection->getSize();
    }

    /**
     * Get Total Wins
     *
     * @return int
     */
    public function getTotalWins()
    {
        $spinId = $this->getRequest()->getParam('spin_id');
        $collection = $this->reportsFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('spin_id', $spinId)
                                ->addFieldToFilter('result', 1);
        return $collection->getSize();
    }

    /**
     * Get Total Orders
     *
     * @return number
     */
    public function getTotalOrders()
    {
        $spinId = $this->getRequest()->getParam('spin_id');
        $collection = $this->reportsFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('spin_id', $spinId)
                                ->addFieldToFilter('order_id', ['neq' => 'NULL']);
        return $collection->getSize();
    }

    /**
     * Get Total Sales
     *
     * @return float
     */
    public function getTotalSales()
    {
        $spinId = $this->getRequest()->getParam('spin_id');
        $amounts = $this->reportsFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('spin_id', $spinId)
                                ->addFieldToFilter('order_amount', ['neq' => 'NULL'])
                                ->getColumnValues('order_amount');
        $total = 0;
        foreach ($amounts as $amount) {
            $total += $amount;
        }
        return $this->pricing->currency($total, true, false);
    }

    /**
     * Get Grid Url
     *
     * @return \Magento\Framework\UrlInterface
     */
    public function getGridUrl()
    {
        return $this->getUrl('spintowin/report/gridreset', ['_current' => true]);
    }
    
    /**
     * Get Row Url
     *
     * @param collection $item
     * @return boolean
     */
    public function getRowUrl($item)
    {
        return false;
    }
}
