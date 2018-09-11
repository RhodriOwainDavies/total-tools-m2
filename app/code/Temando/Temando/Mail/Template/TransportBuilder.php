<?php
namespace Temando\Temando\Mail\Template;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    public function addAttachment($body, $mimeType, $disposition, $encoding, $filename)
    {
        $this->message->createAttachment($body, $mimeType, $disposition, $encoding, $filename);
        return $this;
    }
}
