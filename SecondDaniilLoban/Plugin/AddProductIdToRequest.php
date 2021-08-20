<?php

namespace Amasty\SecondDaniilLoban\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Data\Form\FormKey;

class AddProductIdToRequest
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var FormKey
     */
    private $formKey;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        FormKey $formKey
    ) {
        $this->productRepository = $productRepository;
        $this->formKey = $formKey;
    }

    private function getProductId(string $sku)
    {
        $product = $this->productRepository->get($sku);
        if ($product) {
            return $product->getId();
        }
        return -1;
    }

    public function beforeExecute(
        $subject
    ) {
        $params = $subject->getRequest()->getParams();
        if (isset($params["sku"]) && !isset($params["product"])) {
            $subject->getRequest()->setParam('product', $this->getProductId($params["sku"]));
        }
    }
}
