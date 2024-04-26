<?php

namespace Webkul\SpinToWin\Model\ResourceModel\Reports\Grid;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Psr\Log\LoggerInterface as Logger;
use Webkul\SpinToWin\Model\ResourceModel\Reports;


class Collection extends SearchResult{
    /**
     * @var TimezoneInterface
     */
    private $timeZone;

    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     * @param TimezoneInterface|null $timeZone
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'spintowin_reports',
        $resourceModel = Reports::class,
        TimezoneInterface $timeZone = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
        $this->timeZone = $timeZone ?: ObjectManager::getInstance()
            ->get(TimezoneInterface::class);
    }

    public function _construct(){
        parent::_construct();
        $this->addFilterToMap('email','main_table.email');
    }

    protected function _renderFiltersBefore()
    {
        $joinTable = $this->getTable('spintowin_product_address');
        $this->getSelect()->joinLeft(['address' => $joinTable],'main_table.entity_id = address.reports_id',
                ['address_id','firstname','lastname','street','city','region','postcode','telephone','country_id'])
            ->where('main_table.result = ?',1)->where('main_table.spin_type = ?',2);
        parent::_renderFiltersBefore(); // TODO: Change the autogenerated stub
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();
        foreach ($this->_items as &$item) {
            // if(!empty($item['address_id'])){
            //     $item['address'] = $item['firstname'].' '.$item['lastname'].' '.$item['telephone'].' '
            //     .$item['street'].' '.$item['city'].' '.$item['region'].' '.$item['country_id'].' '.$item['postcode'];
            // }
            $item['timestamp'] = $this->timeZone->date($item['timestamp'])->format('Y-m-d H:i:s');
        }

    }

}