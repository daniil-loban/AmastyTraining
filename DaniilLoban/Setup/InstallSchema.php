<?php

namespace Amasty\DaniilLoban\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

  const TABLE_NAME = 'Amasty_DaniilLoban_blacklist';

  public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
  {
    $setup->startSetup();
    $table = $setup->getConnection()
      ->newTable($setup->getTable('Amasty_DaniilLoban_blacklist'))
      ->addColumn(
        'sku',
        Table::TYPE_TEXT,
        20,
        [
          'nullable' => false,
          'primary' => true,
          'unique' => true
        ],
        'product sku'
      )
      ->addColumn(
        'limit',
        Table::TYPE_INTEGER,
        null,
        [
          'nullable' => false,
          'unsigned' => true,
          'default' => 0
        ],
        'product limit'
      )
      ->setComment('Blacklist table');
    $setup->getConnection()->createTable($table);

    $setup->endSetup();
  }
}
