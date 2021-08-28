<?php

namespace Amasty\DaniilLoban\Model;

use Magento\Framework\Model\AbstractModel;

class BlacklistItem extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(
            ResourceModel\BlacklistItem::class
        );
    }
}
