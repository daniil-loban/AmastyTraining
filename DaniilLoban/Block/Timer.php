<?php
namespace Amasty\DaniilLoban\Block;

class Timer extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getText()
    {
        return "There will be a timer soon, next time! The 🕑ne is late. :)";
    }
}