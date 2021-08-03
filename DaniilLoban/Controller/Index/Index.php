<?php
namespace Amasty\DaniilLoban\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
  public function execute()
  {
    echo 'Привет Magento. Привет, Amasty. Я готов тебя покорить!';
    exit();
  }
}