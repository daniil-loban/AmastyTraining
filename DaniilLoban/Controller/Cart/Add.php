<?php

namespace Amasty\DaniilLoban\Controller\Cart;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

class Add extends Action
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

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        CheckoutSession $messageManager,
        ProductRepositoryInterface $productRepository,
        ProductCollectionFactory $productCollectionFactory
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->session = $messageManager;
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    private function getProuctByCollection($sku)
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToFilter("sku", ["$sku"]);
        $productCollection->addAttributeToSelect("name")
            ->addAttributeToSelect("type_id");
        return $productCollection->getFirstItem();
    }

    private function redirectToIndex()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath("*/index/index");
        return $resultRedirect;
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if (!isset($params["sku"]) ||  !isset($params["qty"])) {
            $this->helper->log("Error");
            return false;
        }

        $sku = $params["sku"];
        $qty = (int)$params["qty"];

        $quote = $this->session->getQuote();
        if (!$quote->getId()) {
            $quote->save();
        }
        /**
         * @var \Magento\Catalog\Model\Product
         */
        $product = null;
        try {
            $product = $this->getProuctByCollection($sku); // $product = $this->productRepository->get($sku); 
            if (!$product->getSku()) {
                throw new NoSuchEntityException(__("The product doesn't exist. Verify and try again."));
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addExceptionMessage($e /*, "The product not found"*/);
            return $this->redirectToIndex();
        }
        if ($product->getTypeID() === "simple") {
            try {
                $quote->addProduct($product, $qty);
                $quote->save();
                $this->messageManager->addSuccessMessage("Product added to cart");
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e);
            }
        } else {
            $this->messageManager->addWarningMessage("The product must be of a simple type");
        }

        return $this->redirectToIndex();
    }
}
