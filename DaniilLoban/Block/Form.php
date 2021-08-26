<?php

namespace Amasty\DaniilLoban\Block;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Form extends Template
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
    }

    public function isShowQty()
    {
        return $this->scopeConfig->getValue("daniilloban_config/general/is_show_qty");
    }

    public function getDefaultQty()
    {
        return (int)$this->scopeConfig->getValue("daniilloban_config/general/default_qty");
    }
    public function getFormAction()
    {
        return $this->getUrl("*/cart/add");
    }
}
