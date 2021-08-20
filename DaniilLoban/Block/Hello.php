<?php

namespace Amasty\DaniilLoban\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;

class Hello extends Template
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
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }

    public function sayHello($name)
    {
        return "Hello, world! My name is $name!";
    }

    public function sayHelloFromConfig($name)
    {
        return $this->scopeConfig->getValue("daniilloban_config/general/greeting_text");
    }
}
