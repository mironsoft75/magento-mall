<?php
namespace Webkul\SpinToWin\Controller\Adminhtml\Code;

use Exception;
use Webkul\SpinToWin\Controller\Adminhtml\Action as WinAction;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Webkul\SpinToWin\Model\ResourceModel\Code\CollectionFactory;

class Save extends WinAction implements HttpPostActionInterface{

    protected $fileSystem;

    protected $uploaderFactory;

    protected $request;

    protected $adapterFactory;
    protected $xlsxUtil;
    protected CollectionFactory $collectionFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        AdapterFactory $adapterFactory,
        Xlsx $xlsxUtil,
        CollectionFactory $collectionFactory

    ) {
        parent::__construct($context);
        $this->fileSystem = $fileSystem;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->xlsxUtil = $xlsxUtil;
        $this->collectionFactory = $collectionFactory;
    }


    public function execute(){
        if ( (isset($_FILES['data']['name'])) && ($_FILES['data']['name'] != '') )
        {
           try
          {
               $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'data']);
               $uploaderFactory->setAllowedExtensions(['xls','xlsx','csv']);
               $uploaderFactory->setAllowRenameFiles(true);
               $uploaderFactory->setFilesDispersion(true);

               $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
               $destinationPath = $mediaDirectory->getAbsolutePath('cloud_code_importdata');

               $result = $uploaderFactory->save($destinationPath);

               if (!$result)
                  {
                    throw new LocalizedException
                    (
                       __('File cannot be saved to path: $1', $destinationPath)
                    );

                  }
               else
                   {
                       $imagePath = 'cloud_code_importdata'.$result['file'];

                       $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);

                       $destinationfilePath = $mediaDirectory->getAbsolutePath($imagePath);

                       /* file read operation */

                    //    $f_object = fopen($destinationfilePath, "r");
                       $spreadsheet = $this->xlsxUtil->load($destinationfilePath);

                       $columns =$spreadsheet->getSheet(0)->toArray();

                       // column name must be same as the Sample file name

                       if(count($columns))
                       {
                           $count = 0;
                           $spinId = $this->request->getParam('spin_id') ?? 0;
                           $err= [];
                           foreach($columns as $i =>$column){

                               if($i && $column[0]){
                                   $code = trim($column[0]);
                                   $cloud_type = trim($column[1]);
                                   $codeObj = $this->collectionFactory->create()->addFieldToFilter('code',$code)->getFirstItem();
                                   if($codeObj->hasData()){
                                       $err[]= $code;
                                   }else {
                                       $codeObj->setData([
                                           'code' => $code,
                                           'spin_id' => $spinId,
                                           'cloud_type' => $cloud_type,
                                           'status' => 0
                                       ]);
                                       $codeObj->save();
                                       ++$count;
                                   }

                               }
                           }
                           $this->messageManager->addSuccess(__('A total of %1 record(s) have been Added.', $count));
                           if(!empty($err)){
                               $this->messageManager->addError(__('this error recode %1',json_encode($err)));
                           }
                           $this->_redirect('*/*/index');
                       }
                       else
                       {
                           $this->messageManager->addError(__("File hase been empty"));
                           $this->_redirect('*/*/index');
                       }

                   }

          }
          catch (\Exception $e)
         {
              $this->messageManager->addError(__($e->getMessage()));
              $this->_redirect('*/*/index');
         }

        }
        else
        {
           $this->messageManager->addError(__("Please try again."));
           $this->_redirect('*/*/index');
        }
   }

}
