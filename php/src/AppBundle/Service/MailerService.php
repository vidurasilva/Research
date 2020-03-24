<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\File;
use UserBundle\Entity\User;

class MailerService
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \Swift_Mailer $mailer
     */
    protected $mailer;

    /**
     * @var string
     */
    protected $fromEmail;

    /**
     * @var string
     */
    protected $fromName;

    /**
     * @param \Twig_Environment $twig
     * @param \Swift_Mailer $mailer
     * @param string $fromEmail
     * @param string $fromName
     */
    public function __construct(\Twig_Environment $twig, \Swift_Mailer $mailer, $fromEmail, $fromName)
    {
        $this->twig      = $twig;
        $this->mailer    = $mailer;
        $this->fromEmail = $fromEmail;
        $this->fromName  = $fromName;
    }

    /**
     * @param User $user
     * @param string $subject
     * @param string $template
     * @param array $parameters
     */
    public function send(User $user, $subject, $template, $parameters = [])
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->getFromEmail(), $this->getFromName())
            ->setTo($user->getEmail(), sprintf('%s %s', $user->getFirstname(), $user->getLastname()))
            ->setBody($this->renderTemplate($template, $parameters), 'text/html');

        $this->mailer->send($message);
    }

    public function sendToEmail($email, $subject, $template, $parameters)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->getFromEmail(), $this->getFromName())
            ->setTo($email)
            ->setBody($this->renderTemplate($template, $parameters), 'text/html');

        return $this->mailer->send($message);
    }

    public function sendCheckinListToSupervisor($email, $subject, $template, $parameters)
    {
    	$message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($this->getFromEmail(), $this->getFromName())
            ->setTo($email)
		    ->setContentType('text/html')
		    ->setCharset('UTF-8')
            ->setBody($this->renderTemplate($template, $parameters), 'text/html');

        return $this->mailer->send($message);
    }

    /**
     * @param string $template
     * @param array $parameters
     * @return string
     */
    protected function renderTemplate($template, $parameters = [])
    {
        return $this->twig->render($template, $parameters);
    }

    /**
     * @return string
     */
    public function getFromName()
    {
        return $this->fromName;
    }

    /**
     * @param string $fromName
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
    }

    /**
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * @param string $fromEmail
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
    }
}
