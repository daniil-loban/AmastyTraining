<?php

namespace Amasty\SecondDaniilLoban\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;

class PromptPromo
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var EventManager
     */
    private $eventManager;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        EventManager $eventManager

    ) {
        $this->productRepository = $productRepository;
        $this->eventManager = $eventManager;
    }

    private function getProductId(string $sku)
    {
        $product = $this->productRepository->get($sku);
        if ($product) {
            return $product->getId();
        }
        return -1;
    }

    public function afterExecute(
        $subject,
        $result
    ) {
        $params = $subject->getRequest()->getParams();
        if (!isset($params['sku'])) {
            return $result;
        }
        $sku = $params['sku'];
        $productId = $this->getProductId($sku);
        if ($productId !== -1) {
            $this->eventManager->dispatch(
                'amasty_daniilloban_product_added',
                ['productSKU' => $sku]
            );
        }
        return $result;
    }
}
