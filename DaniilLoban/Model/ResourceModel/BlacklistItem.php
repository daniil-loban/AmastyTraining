<?php

namespace Amasty\DaniilLoban\Model\ResourceModel;

class BlacklistItem extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
  protected function _construct()
  {
    $this->_init(
      \Amasty\DaniilLoban\Setup\InstallSchema::TABLE_NAME,
      'sku'
    );
  }
}
