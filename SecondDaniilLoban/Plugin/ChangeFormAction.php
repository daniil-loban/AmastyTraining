<?php

namespace Amasty\SecondDaniilLoban\Plugin;

use \Magento\Framework\UrlInterface;

class ChangeFormAction
{
    /**
     * @var UrlInterface;
     */
    private $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }
    /*
    public function beforeGetFormAction($subject)
    {
        return [];
    }
    */
    public function aroudGetFormAction(
        $subject,
        callable $proceed
    ) {
        return; //skip
    }
    public function afterGetFormAction(
        $subject,
        $result
    ) {
        return $this->urlBuilder->getUrl('checkout/cart/add');
    }
}
