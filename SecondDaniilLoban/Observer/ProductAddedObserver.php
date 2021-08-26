<?php

namespace Amasty\SecondDaniilLoban\Observer;


use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Checkout\Model\Cart;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductAddedObserver implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var FormKey
     */
    private $formKey;
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var Product
     */
    private $product;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var CheckoutSession
     */
    private $session;
    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var RequestInterface
     */
    public $request;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ProductRepositoryInterface $productRepository,
        CheckoutSession $messageManager,
        ProductCollectionFactory $productCollectionFactory,
        RequestInterface $request
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
        $this->session = $messageManager;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->request = $request;
    }

    private function getPromoSKU()
    {
        return $this->scopeConfig->getValue('seconddaniilloban_config/general/promo_sku');
    }

    private function getForSKUList()
    {
        $for_sku = $this->scopeConfig->getValue('seconddaniilloban_config/general/for_sku');
        return explode(',', $for_sku);
    }

    private function getProuctByCollection($sku)
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToFilter('sku', [$sku]);
        $productCollection->addAttributeToSelect('name')
            ->addAttributeToSelect('type_id');
        return $productCollection->getFirstItem();
    }

    private function addPromoProduct(string $sku)
    {
        $quote = $this->session->getQuote();
        if (!$quote->getId()) {
            $quote->save();
        }
        $product = $this->getProuctByCollection($sku);
        $quote->addProduct($product, 1);
        $quote->save();
    }

    public function execute(Observer $observer)
    {
        $promoSKU = $this->getPromoSKU();
        $skuList = $this->getForSKUList();
        $productSKU = $observer->getData('productSKU');
        if (in_array($productSKU, $skuList)) {
            $this->addPromoProduct($promoSKU);
        }
    }
}
