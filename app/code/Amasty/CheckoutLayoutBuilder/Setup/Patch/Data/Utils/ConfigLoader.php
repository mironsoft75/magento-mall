<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutLayoutBuilder
*/

declare(strict_types=1);

namespace Amasty\CheckoutLayoutBuilder\Setup\Patch\Data\Utils;

use Amasty\CheckoutLayoutBuilder\Model\ConfigProvider;

class ConfigLoader
{
    /**
     * @var ConfigManager
     */
    private $configManager;

    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param string $configTableName
     * @return array
     */
    public function loadConfig(\Magento\Framework\DB\Adapter\AdapterInterface $connection, $configTableName): array
    {
        $blockNamesBlockPath = ConfigProvider::PATH_PREFIX . 'block_names/';
        $designBlockPath = ConfigProvider::PATH_PREFIX . ConfigProvider::DESIGN_BLOCK;

        $select = $connection->select()
            ->from($configTableName)
            ->where('path LIKE "' . $blockNamesBlockPath . '%"')
            ->orWhere('path IN(?)', [
                $designBlockPath . ConfigProvider::FIELD_CHECKOUT_DESIGN,
                $designBlockPath . ConfigProvider::FIELD_CHECKOUT_LAYOUT,
                $designBlockPath . ConfigProvider::FIELD_CHECKOUT_LAYOUT_MODERN
            ]);

        $configRows = $connection->fetchAll($select);
        $scopedConfig = $this->configManager->reorderConfigRowsToScopedConfig($configRows);

        if (!isset($scopedConfig['default_0'])) {
            $scopedConfig['default_0'] = [];
        }

        $scopedConfig = $this->configManager->sortConfigGlobalScopeFirst($scopedConfig);
        $scopedConfig = $this->setValuesForAllFieldsInEachScope($scopedConfig);

        return $scopedConfig;
    }

    /**
     *  Check on exist Layout Builder configs
     */
    public function isExistBuilder(
        \Magento\Framework\DB\Adapter\AdapterInterface $connection,
        string $configTableName,
        string $builderConfigPath,
        string $frontendConfigPath
    ): bool {
        $select = $connection->select()
            ->from($configTableName)
            ->where('path LIKE "' . $builderConfigPath . '%"')
            ->orWhere('path LIKE "' . $frontendConfigPath . '%"');

        $configRows = $connection->fetchAll($select);

        if (empty($configRows)) {
            return false;
        }

        return true;
    }

    /**
     * This method sets unspecified values for all configs. It tries to get value from parent scope
     * (for website = default, for store = website then default) and if there is no value it gets
     * value from default values.
     * In this way all fields will be specified.
     *
     * @param array $scopedConfig
     * @return array
     */
    private function setValuesForAllFieldsInEachScope($scopedConfig): array
    {
        $fieldsWithDefaultValue = $this->configManager->getDefaultValues();

        foreach ($scopedConfig as $scopeKey => &$scopeValues) {
            list($scope, $scopeId) = explode('_', $scopeKey);

            foreach ($fieldsWithDefaultValue as $field => $defaultValue) {
                if (!array_key_exists($field, $scopeValues)) {
                    $value = $this->configManager->getParentScopeValue(
                        $scopedConfig,
                        $scope,
                        $scopeId,
                        $field
                    );
                    $scopeValues[$field] = $value;
                }

                if ($scopeValues[$field] === null) {
                    $scopeValues[$field] = $defaultValue;
                }
            }
        }

        return $scopedConfig;
    }
}
