<?php

namespace Application\Service;

use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;

class MailService
{
    public function sendEmail($msgSubj, $msgText, $fromEmail, $toEmail)
    {
        $message = new Message();
        $message->setBody($msgText);
        $message->setFrom($fromEmail, $fromEmail);
        $message->addTo($toEmail, $toEmail);
        $message->setSubject($msgSubj);
        $transport = new Sendmail();
        $transport->send($message);

        return true;
    }
}
