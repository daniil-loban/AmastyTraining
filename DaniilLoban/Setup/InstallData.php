<?php

namespace Amasty\DaniilLoban\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $dataNewsRows = [
            [
                'sku' => 'MJ01-M-Red',
                'limit' => 5
            ],
            [
                'sku' => 'MJ01-M-Orange',
                'limit' => 0
            ]
        ];

        foreach ($dataNewsRows as $data) {
            $setup->getConnection()->insert($setup->getTable('Amasty_DaniilLoban_blacklist'), $data);
        }
    }
}
