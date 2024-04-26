<?php
namespace Webkul\SpinToWin\Controller\Adminhtml\Upload;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Controller\ResultFactory;

class Save extends Action{
    protected $uploaderFactory;
    protected $adapterFactory;
    protected $filesystem;
    protected $resultFactory;

    public function __construct(
        Context $context,
        UploaderFactory $uploaderFactory,
        AdapterFactory $adapterFactory,
        ResultFactory $resultFactory,
        Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->adapterFactory = $adapterFactory;
        $this->filesystem = $filesystem;
        $this->resultFactory = $resultFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }
    public function execute()
    {
        
        if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
            try{
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'image']);
                $uploaderFactory->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $imageAdapter = $this->adapterFactory->create();
                // $uploaderFactory->addValidateCallback('custom_image_upload',$imageAdapter,'validateUploadFile');
                $uploaderFactory->setAllowRenameFiles(true);
                $uploaderFactory->setFilesDispersion(true);
                $mediaDirectory = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath('webkul/spin');
                $result = $uploaderFactory->save($destinationPath);
                if (!$result) {
                    throw new LocalizedException(
                        __('File cannot be saved to path: $1', $destinationPath)
                    );
                }
                $imagePath = 'webkul/spin'.$result['file'];
                $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $path = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$imagePath;
                $resultJson->setData(['url' => $path,'file' =>$imagePath]);
                return $resultJson;
                
            } catch (\Exception $e) {
            }
        }
        
    }
}