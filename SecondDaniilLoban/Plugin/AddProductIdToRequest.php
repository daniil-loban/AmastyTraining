<?php

namespace Amasty\SecondDaniilLoban\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Event\ManagerInterface as EventManager;

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

    /**
     * @var EventManager
     */
    private $eventManager;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        FormKey $formKey,
        EventManager $eventManager

    ) {
        $this->productRepository = $productRepository;
        $this->formKey = $formKey;
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

    public function beforeExecute(
        $subject
    ) {
        $params = $subject->getRequest()->getParams();
        if (isset($params['sku']) && !isset($params['product'])) {
            $sku = $params['sku'];
            $productId = $this->getProductId($sku);

            if ($productId !== -1) {
                $subject->getRequest()->setParam('product', $productId);
                $this->eventManager->dispatch(
                    'amasty_daniilloban_product_added',
                    ['productSKU' => $sku]
                );
            }
        }
    }
}
