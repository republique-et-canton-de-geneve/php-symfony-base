<?php

namespace App\Service;

use App\Application;
use App\Parameter;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Throwable;

class Mail
{
    protected MailerInterface $mailer;
    protected Application $application;
    protected Parameter $parameter;
    protected LoggerInterface $logger;

    public function __construct(
        MailerInterface $mailer,
        Application $application,
        Parameter $parameter,
        LoggerInterface $applicationLogger,
    ) {
        $this->mailer = $mailer;
        $this->application = $application;
        $this->parameter = $parameter;
        $this->logger = $applicationLogger;
    }

    /**
     * Send an email with the symfony mailer
     * if the system doesn't run on production server, all mail are rerouted to the value of parameter
     *  smtpRedirectMailTo. On your local PC you can set
     * 'APP_MAILER_DSN' => 'null://null' in you env.local.php file to bypass the mail.
     *
     * @param string|string[] $to
     *
     * @throws Throwable
     * @throws TransportExceptionInterface
     */
    public function sendMail(string|array $to, string $subject, string $body): void
    {
        try {
            $dest = $to;
            $log = '';
            $strTo = is_array($to) ? implode(', ', $to) : $to;
            if ('prod' !== $this->application->getServerType() || $this->parameter->smtpForceRedirection) {
                // redirect allways a mail to a specific mailbox
                $dest = $this->parameter->smtpRedirectMailTo;
                $log = 'Message redirigé à ' . $dest . 'au lieu du ';
                $body = "This e-mail was rerouted.\n" .
                    'Server type: ' . $this->application->getServerType() . "\n" .
                    'Web site: ' . $this->application->getUrl() . "\n" .
                    'Originally to: ' . $strTo . "\n" .
                    "-----------------------\n" . $body;
            }
            $log .= 'message destiné à ' . $strTo;
            $this->logger->info($log, ['subject' => $subject, 'body' => $body]);
            if (is_string($dest)) {
                $dest = [$dest];
            }
            $email = (new Email())
                ->to(...$dest)
                ->subject($subject)
                ->text($body);

            $this->mailer->send($email);
        } catch (Throwable $e) {
            $this->logger->error(
                "Lors de l'envoi du mail l'erreur suivant a été détectée :" . $e->getMessage(),
                (array) $e
            );
            throw $e;
        }
    }
}
