<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Webkul\SpinToWin\Controller\Adminhtml\Report;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Webkul\SpinToWin\Controller\Adminhtml\Report\AbstractReport;

class ExportCsv extends AbstractReport
{
    /**
     * Export  report grid to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'spintowin_report.csv';
        $grid = $this->_view->getLayout()->createBlock(\Webkul\SpinToWin\Block\Adminhtml\Report::class);
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getCsvFile(), DirectoryList::VAR_DIR);
    }
   
}
