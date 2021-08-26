<?php

namespace Amasty\DaniilLoban\Model\ResourceModel\BlacklistItem;


use \Amasty\DaniilLoban\Model\BlacklistItem;
use \Amasty\DaniilLoban\Model\ResourceModel\BlacklistItem as BlacklistItemResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            BlacklistItem::class,
            BlacklistItemResource::class
        );
    }
}
