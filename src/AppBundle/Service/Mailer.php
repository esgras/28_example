<?php

namespace AppBundle\Service;


class Mailer
{
    private $mailer;
    private $from;
    private $defaultName;

    private $message;

    public function __construct(\Swift_Mailer $mailer, string $from, $defaultName)
    {
        $this->mailer = $mailer;
        $this->from = $from;
        $this->defaultName = $defaultName;
    }

    public function send($subject, $to, $body, $contentType='text/html', $from='', $defaultName='')
    {
        $from = empty($from) ? $this->from : $from;
        $defaultName = empty($defaultName) ? $this->defaultName : $defaultName;

        $this->createMessage($subject, $body, $contentType, $from, $defaultName);
        $this->message->setTo($to);

        $this->mailer->send($this->message);
    }

    public function createMessage($subject, $body, $contentType='text/html', $from='', $defaultName='')
    {
        $from = empty($from) ? $this->from : $from;
        $defaultName = empty($defaultName) ? $this->defaultName : $defaultName;

        $this->message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from, $defaultName)
            ->setBody($body)
            ->setContentType($contentType);

        return $this;
    }

    public function addFile($filePath)
    {
        $this->message->attach(\Swift_Attachment::fromPath($filePath));
        return $this;
    }


    public function sendTo($to)
    {
        $this->message->setTo($to);
        $this->mailer->send($this->message);
    }
}