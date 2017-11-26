<?php 
namespace Application\Mvc;

use Zend\EventManager\Event;

class MailForgotPasswordEvent extends Event
{
    const EVENT_MAIL_FORGOT_PASSWORD_PRE = 'mailSendForgotPassword.pre';
    const EVENT_MAIL_FORGOT_PASSWORD_POST = 'mailSendForgotPassword.post';
}