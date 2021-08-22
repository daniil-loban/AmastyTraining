<?php

namespace Amasty\SecondDaniilLoban\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;

class AddProductIdToRequest
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    public function __construct(
        ProductRepositoryInterface $productRepository
    ) {
        $this->productRepository = $productRepository;
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
        if (isset($params['sku']) && !isset($params['product'])) {
            $sku = $params['sku'];
            $productId = $this->getProductId($sku);
            if ($productId !== -1) {
                $subject->getRequest()->setParam('product', $productId);
            }
        }
    }
}
