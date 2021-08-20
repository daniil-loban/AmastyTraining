<?php

namespace Amasty\DaniilLoban\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Index extends Action
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }

    public function execute()
    {
        if ($this->scopeConfig->isSetFlag("daniilloban_config/general/enabled")) {
            return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        } else {
            die("Sorry, the component is disabled.");
        }
    }
}
