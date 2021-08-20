<?php

namespace Amasty\SecondDaniilLoban\Plugin;

use Magento\Framework\UrlInterface;

class ChangeFormAction
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var UrlInterface;
     */
    private $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    public function afterGetFormAction(
        $subject,
        $result
    ) {
        return $this->urlBuilder->getUrl("checkout/cart/add");
    }
}
