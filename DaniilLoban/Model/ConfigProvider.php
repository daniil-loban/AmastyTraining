<?php

namespace Amasty\DaniilLoban\Model;

use \Amasty\DaniilLoban\Model\ConfigProviderAbstract;
use Magento\Checkout\Model\Session;

class ConfigProvider extends ConfigProviderAbstract
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(
        //ScopeConfigInterface $scopeConfig
        Session $messageManager
    ) {
        $this->session = $messageManager;
    }

    public function getConfig()
    {
        $storeId = $this->session->getStoreId();
        return [
            "enabled" => $this->getIsEnabled($storeId),
            "is_show_qty" => $this->getIsShowQtyField($storeId),
            "default_qty" => $this->getQtyFieldDefaultValue($storeId)
        ];
    }

    public function getIsEnabled($storeId)
    {
        return (bool)$this->GetValue("/general/enabled", $storeId);
    }

    public function getIsShowQtyField($storeId)
    {
        return (bool)$this->GetValue("/general/is_show_qty", $storeId);
    }

    public function getQtyFieldDefaultValue($storeId)
    {
        return (int)$this->GetValue("/general/default_qty", $storeId);
    }
}
