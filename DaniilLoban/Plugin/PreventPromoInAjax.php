<?php

namespace Amasty\DaniilLoban\Plugin;

class PreventPromoInAjax
{
    public function aroundExecute(
        $subject,
        $proceed,
        $observer
    ) {
        if (!$subject->request->isAjax()) {
            $proceed($observer);
        }
    }
}
