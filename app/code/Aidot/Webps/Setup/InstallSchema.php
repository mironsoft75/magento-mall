<?php

namespace Aidot\Webps\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
class InstallSchema  implements InstallSchemaInterface{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $tableName= 'image_webps_log';
        if(!$setup->tableExists($tableName)){
            $table = $setup->getConnection()->newTable($tableName)
            ->addColumn('id',Table::TYPE_INTEGER,null,[
                'identity' => true,
                    'nullable' => false,
                    'primary'  => true,
                    'unsigned' => true,
            ],"primary id")
            ->addColumn('image',Table::TYPE_TEXT,255,['nullable' => false],'Image')
            ->addColumn('image_path',Table::TYPE_TEXT,255,['nullable' => false],'Image Path')
            ->addColumn('status',Table::TYPE_SMALLINT,1,['nullable' => true,'default' => 0],'Status 0 No 1 Yes ')
            ->addColumn('updated_at',Table::TYPE_TIMESTAMP,null,['nullable' => false,'default' => Table::TIMESTAMP_INIT],'Updated At')
            ->addColumn('created_at',Table::TYPE_TIMESTAMP,null,['nullable' => false,'default' => Table::TIMESTAMP_INIT],'Created At')
            ->setComment(
                'Image Webps Log'
            );
            $setup->getConnection()->createTable($table);
        }
        $setup->endSetup();
    }
}