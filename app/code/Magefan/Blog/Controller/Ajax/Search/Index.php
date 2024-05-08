<?php
namespace Magefan\Blog\Controller\Ajax\Search;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magefan\Blog\App\Action\Action{

    protected $resultFactory;
    protected $postList;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magefan\Blog\Block\Search\PostList $postList,
        ResultFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->postList = $postList;
        $this->resultFactory = $jsonFactory;
    }

    public function execute()
    {
        $connection = $this->postList->getPostCollection();
        $count = $connection->count();
        $result= $connection->load()->getData();
        $back=['code' => 200,"message" =>'success','data'=>['list' => [],'count' => $count]];
        if(!empty($result)){
            $result = array_slice($result,0,5);
           foreach($result as $item){
               $back['data']['list'][]= $item;
           } 
        }
        
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($back);
        return $resultJson;
    }

}