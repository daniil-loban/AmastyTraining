<?php

namespace Amasty\DaniilLoban\Model;

use \Amasty\DaniilLoban\Model\BlacklistItemFactory;
use \Amasty\DaniilLoban\Model\ResourceModel\BlacklistItem as BlacklistItemResource;

class BlacklistItemRepository
{
    /**
     *  @var BlacklistItemFactory
     */
    private $blacklistItemFactory;

    /**
     * @var BlacklistItemResource
     */
    private $blacklistItemResource;

    public function __construct(
        BlacklistItemFactory $blacklistItemFactory,
        BlacklistItemResource $blacklistItemResource
    ) {
        $this->blacklistItemFactory = $blacklistItemFactory;
        $this->blacklistItemResource = $blacklistItemResource;
    }

    public function getBySku(string $sku): BlacklistItem
    {
        $blacklistItem = $this->blacklistItemFactory->create();
        $this->blacklistItemResource->load($blacklistItem, $sku, 'sku');
        return $blacklistItem;
    }

    public function saveEmailBodyForSku(string $sku, string $emailBody)
    {
        $blacklistItem = $this->getBySku($sku);
        $blacklistItem->setData('email_body', $emailBody);
        $this->blacklistItemResource->save($blacklistItem);
    }

    public function deleteBySku(string $sku)
    {
        $blacklistItem  = $this->getBySku($sku);
        $this->blacklistItemResource->delete($blacklistItem);
    }
}
