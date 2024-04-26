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

class ExportExcel extends AbstractReport
{
    /**
     * Export  report grid to Excel XML format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $fileName = 'spintowin_report.xml';
        $grid = $this->_view->getLayout()->createBlock(\Webkul\SpinToWin\Block\Adminhtml\Report::class);
        $this->_initReportAction($grid);
        return $this->_fileFactory->create($fileName, $grid->getExcelFile($fileName), DirectoryList::VAR_DIR);
    }
}
