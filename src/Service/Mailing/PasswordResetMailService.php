<?php
declare(strict_types = 1);

namespace App\Service\Mailing;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PasswordResetMailService
{
    private string $adminEmail;

    private MailerInterface $mailer;

    private TranslatorInterface $translator;

    /**
     * MailingService constructor.
     * @param string $adminEmail
     * @param MailerInterface $mailer
     * @param TranslatorInterface $translator
     */
    public function __construct(
        string $adminEmail,
        MailerInterface $mailer,
        TranslatorInterface $translator
    ) {
        $this->adminEmail = $adminEmail;
        $this->mailer = $mailer;
        $this->translator = $translator;
    }

    /**
     * @param string $email
     * @param string $passwordResetHash
     * @throws TransportExceptionInterface
     */
    public function sendPasswordResetEmail(string $email, string $passwordResetHash): void
    {
        $notifyEmail = new NotificationEmail();
        $notifyEmail->subject($this->translator->trans('lostPassword.email.subject'))
            ->htmlTemplate('email/passwordReset.html.twig')
            ->from($this->adminEmail)
            ->to($email)
            ->context([
                'passwordResetHash' => $passwordResetHash,
            ]);

        $this->mailer->send($notifyEmail);
    }
}
