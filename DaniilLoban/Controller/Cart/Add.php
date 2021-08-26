<?php

namespace Amasty\DaniilLoban\Controller\Cart;

use \Amasty\DaniilLoban\Model\ResourceModel\BlacklistItem\CollectionFactory as BlacklistCollectionFactory;
use Exception;
use FFI\Exception as FFIException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

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

    /**
     *  @var BlacklistCollectionFactory
     */
    private $blacklistCollectionFactory;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        CheckoutSession $messageManager,
        ProductRepositoryInterface $productRepository,
        ProductCollectionFactory $productCollectionFactory,
        BlacklistCollectionFactory $blacklistCollectionFactory
    ) {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
        $this->session = $messageManager;
        $this->productRepository = $productRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->blacklistCollectionFactory = $blacklistCollectionFactory;
    }

    private function getBlackList($sku)
    {
        $blacklist = $this->blacklistCollectionFactory->create();
        $blacklist->addFieldToFilter('sku', ['eq' => $sku]);
        $result = [];
        foreach ($blacklist as $blacklistItem) {
            $result[$blacklistItem->getSku()] = intval($blacklistItem->getLimit());
        }
        return $result;
    }

    private function getCartItems($sku)
    {
        $result = [];
        $cartItems = $this->session->getQuote()->getAllVisibleItems();
        if (count($cartItems) > 0) {
            foreach ($cartItems as $item) {
                if ($item->getSku() === $sku) {
                    $result[$item->getSku()] = $item->getQty();
                    break;
                }
            }
        }
        return $result;
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

    private function limitQty($sku, $qty)
    {
        $allowedQty = $qty;
        $skippedQty = 0;

        $cartItems = $this->getCartItems($sku) ?? 0;
        $blacklist = $this->getBlackList($sku);

        if (empty($blacklist)) {
            return [$skippedQty, $allowedQty, 0];
        }

        if (empty($cartItems)) {
            $allowedQty = $blacklist[$sku];
            if ($qty > $blacklist[$sku]) {
                $skippedQty = $qty - $blacklist[$sku];
            }
        } else if ($cartItems[$sku] + $qty > $blacklist[$sku]) {
            $allowedQty = $blacklist[$sku] - $cartItems[$sku];
            $skippedQty = $cartItems[$sku] + $qty - $blacklist[$sku];
        }
        return [$skippedQty, $allowedQty, $blacklist[$sku]];
    }

    static function getLimitErrorMessage($qty, $skippedQty, $allowedQty, $limitQty)
    {
        if ($skippedQty) {
            return ($allowedQty > 0
                ? "$allowedQty of $qty item(s) added to cart,"
                : 'Items not added to cart, ') . " limit of $limitQty item(s) reached.";
        }
        return '';
    }


    private function getProductBySku($sku)
    {
        /**
         * @var \Magento\Catalog\Model\Product
         */
        $product = null;
        try {
            $product = $this->getProuctByCollection($sku);
            if (!$product->getSku()) {
                throw new NoSuchEntityException(__("The product doesn't exist. Verify and try again."));
            }
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addExceptionMessage($e /*, "The product not found"*/);
        }
        return $product;
    }

    public function execute()
    {

        $params = $this->getRequest()->getParams();
        if (!isset($params["sku"]) ||  !isset($params["qty"])) {
            return false;
        }

        $sku = $params["sku"];
        $qty = (int)$params["qty"];

        $product = $this->getProductBySku($sku);
        if (!$product) {
            return $this->redirectToIndex();
        }

        [$skippedQty, $allowedQty, $limitQty] = $this->limitQty($sku, $qty);
        $limitErrorMessage = self::getLimitErrorMessage($qty, $skippedQty, $allowedQty, $limitQty);
        if ($allowedQty <= 0) {
            $this->messageManager->addExceptionMessage(new Exception($limitErrorMessage));
            return $this->redirectToIndex();
        }


        if ($product->getTypeID() === "simple") {
            $quote = $this->session->getQuote();
            if (!$quote->getId()) {
                $quote->save();
            }
            try {
                $quote->addProduct($product, $qty);
                $quote->save();
                if ($skippedQty) {
                    $this->messageManager->addExceptionMessage(new Exception($limitErrorMessage));
                } else {
                    // $this->messageManager->addSuccessMessage("Product added to cart");
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e);
            }
        } else {
            $this->messageManager->addWarningMessage("The product must be of a simple type");
        }
        return $this->redirectToIndex();
    }
}
