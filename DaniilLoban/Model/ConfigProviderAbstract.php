<?php

namespace Amasty\DaniilLoban\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

abstract class ConfigProviderAbstract implements ConfigProviderInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * @var string
     */
    protected $pathPrefix = "daniilloban_config/";

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    protected function GetValue($path, $storeId, $scope = "store")
    {
        return $this->scopeConfig->getValue(
            $this->pathPrefix . $path,
            $scope,
            $storeId
        );
    }
}
