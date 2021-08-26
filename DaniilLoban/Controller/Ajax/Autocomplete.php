<?php

namespace Amasty\DaniilLoban\Controller\Ajax;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;

class Autocomplete extends Action
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var CheckoutSession
     */
    private $session;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        CheckoutSession $messageManager,
        ProductRepositoryInterface $productRepository,
        ProductCollectionFactory $productCollectionFactory,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->session = $messageManager;
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    private function getProuctsList($sku)
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection
            ->addAttributeToSelect(["name"])
            ->addAttributeToFilter("type_id", ["eq" => "simple"])
            ->addAttributeToFilter("sku", ["like" => $sku . "%"])
            ->setPageSize(20, 1);

        $productCollection->getItems();

        $result = [];
        foreach ($productCollection as $product) {
            $result[] = [
                "name" => $product->getName(),
                "sku" => $product->getSku(),
                /* "type" => $product->getTypeId() */
            ];
        }
        return $result;
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $params = $this->getRequest()->getParams();
        if (!isset($params["sku"])) {
            return $resultJson->setData([]);
        }
        $sku = utf8_decode(urldecode($params["sku"]));
        $products = $this->getProuctsList($sku);
        return $resultJson->setData(["q" => $sku, "list" => $products]);
    }
}
