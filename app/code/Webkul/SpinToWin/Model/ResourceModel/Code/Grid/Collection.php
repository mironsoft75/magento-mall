<?php

namespace Webkul\SpinToWin\Model\ResourceModel\Code\Grid;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Psr\Log\LoggerInterface as Logger;
use Webkul\SpinToWin\Model\ResourceModel\CloudCode;


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
        $mainTable = 'spintowin_cloud_code',
        $resourceModel = CloudCode::class,
        TimezoneInterface $timeZone = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
        $this->timeZone = $timeZone ?: ObjectManager::getInstance()
            ->get(TimezoneInterface::class);
    }

    

    protected function _renderFiltersBefore()
    {
        $joinTable = $this->getTable('spintowin_reports');
        $this->getSelect()->joinLeft(['report' => $joinTable],'main_table.reports_id = report.entity_id',
                ['timestamp','email']);
        parent::_renderFiltersBefore(); // TODO: Change the autogenerated stub
    }

   

}