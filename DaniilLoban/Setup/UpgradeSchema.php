<?php

namespace Amasty\DaniilLoban\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$setup->startSetup();

		if (version_compare($context->getVersion(), '0.0.2', '<')) {
			$setup->getConnection()->addColumn(
				$setup->getTable('Amasty_DaniilLoban_blacklist'),
				'email_body',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'nullable' => true,
					'comment' => 'Email Body'
				]
			);
		}

		$setup->endSetup();
	}
}
