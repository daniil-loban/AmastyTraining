<?php

namespace Amasty\DaniilLoban\Cron;


use \Amasty\DaniilLoban\Model\BlacklistItemRepository;
use \Magento\CatalogInventory\Model\Stock\Item;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class SendBlacklistEmail
{

  /**
   * @var LoggerInterface
   */
  private $logger;
  /**
   * @var ScopeConfigInterface
   */
  private $scopeConfig;

  /**
   * @var BlacklistItemRepository
   */
  private $blacklistItemRepository;

  /**
   * @var StoreManagerInterface
   */
  private $storeManagerInterface;

  /**
   *  @var TransportBuilder 
   */
  private $transportBuilder;

  /**
   *  @var FactoryInterface
   */
  private $templateFactory;

  /**
   * @var ProductCollectionFactory
   */
  private $productCollectionFactory;

  /**
   * @var Item
   */
  protected $stockItem;


  public function __construct(
    LoggerInterface $logger,
    ScopeConfigInterface $scopeConfig,
    BlacklistItemRepository $blacklistItemRepository,
    StoreManagerInterface $storeManagerInterface,
    TransportBuilder $transportBuilder,
    FactoryInterface $templateFactory,
    ProductCollectionFactory $productCollectionFactory,
    Item $stockItem
  ) {
    $this->logger = $logger;
    $this->scopeConfig = $scopeConfig;
    $this->blacklistItemRepository = $blacklistItemRepository;
    $this->storeManagerInterface = $storeManagerInterface;
    $this->transportBuilder = $transportBuilder;
    $this->templateFactory = $templateFactory;
    $this->productCollectionFactory = $productCollectionFactory;
    $this->stockItem = $stockItem;
  }

  private function getProuctBySku($sku)
  {
    $productCollection = $this->productCollectionFactory->create();
    $productCollection->addAttributeToFilter("sku", ["$sku"]);
    $productCollection->addAttributeToSelect("name");
    return $productCollection->getFirstItem();
  }

  private function getQtyInStore($product)
  {
    $stockItem = $this->stockItem->load($product->getId(), 'product_id');
    return $stockItem->getQty();
  }

  public function execute()
  {
    $sku = 'MJ01-M-Red';
    $product = $this->getProuctBySku($sku);
    $blacklistItem = $this->blacklistItemRepository->getBySku($sku);

    $senderName = 'Admin';
    $senderEmail = 'amastyadmin@amasty.com';
    $toEmail = $this->scopeConfig->getValue("daniilloban_config/cron_group/email");
    $tempaleId = $this->scopeConfig->getValue("daniilloban_config/cron_group/email_template");

    $tempaleVars = [
      'blacklistItem' => $blacklistItem,
      'sku' => $blacklistItem->getSku(),
      'name' => $product->getName(),
      'limit' => $blacklistItem->getLimit(),
      'available' => $this->getQtyInStore($product)
    ];

    $from = [
      'email' => $senderEmail,
      'name' => $senderName
    ];

    $storeId = $this->storeManagerInterface->getStore()->getId();

    $templateOptions = [
      'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
      'store' => $storeId
    ];
    /**
     * @var \Magento\Email\Model\Transport $transport
     */
    $transport = $this->transportBuilder->setTemplateIdentifier($tempaleId, ScopeInterface::SCOPE_STORE)
      ->setTemplateOptions($templateOptions)
      ->setTemplateVars($tempaleVars)
      ->setFromByScope($from)
      ->addTo($toEmail)
      ->getTransport();

    //$transport->sendMessage();

    /** @var \Magento\Framework\Mail\EmailMessage $message */
    // $message = $transport->getMessage();
    // $messageText = $message->getBodyText();

    $template =  $this->templateFactory->get($tempaleId);
    $template->setVars($tempaleVars)
      ->setOptions($templateOptions);
    $emailBody = $template->processTemplate();

    $this->blacklistItemRepository->saveEmailBodyForSku($sku, $emailBody);
    $this->logger->debug('Amasty DaniilLoban Job');
  }
}
