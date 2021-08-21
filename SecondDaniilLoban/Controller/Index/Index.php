<?php

namespace Amasty\SecondDaniilLoban\Controller\Index;

use Amasty\DaniilLoban\Controller\Index\Index as DaniilLobanController;

class Index extends DaniilLobanController
{
    public function execute()
    {
        $customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');
        if (!$customerSession->isLoggedIn()) {
            die('Login required to access');
        }

        return parent::execute();
    }
}
