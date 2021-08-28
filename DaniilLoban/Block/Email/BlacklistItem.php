<?php

namespace Amasty\DaniilLoban\Block\Email;

use Magento\Framework\View\Element\Template;

class BlacklistItem extends Template
{
    public function getName()
    {
        return $this->getData('name');
    }
}
