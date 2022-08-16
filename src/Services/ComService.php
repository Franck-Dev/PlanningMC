<?php

namespace App\Services;

use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Notification\Notification;

/**
 * ComService permet de communiquer des informations par mails ou notifications
 */
class ComService
{
    public function __construct(private MailerInterface $mailer, private NotifierInterface $notif)   {} 
    
    /**
     * sendEmail Permet d'envoyer des mails avec un contenu en text ou html
     *
     * @param  string $userMail
     * @param  string $dest
     * @param  string $sujet
     * @param  string $body
     * @param  string $copy
     * @return void
     */
    public function sendEmail($userMail, $dest, $sujet, $body, $copyDest='postmaster.apamc@gmail.com'): void
        {
            $message = (new Email())
            // Sujet du mail
            ->subject($sujet)
            // On attribue l'expéditeur
            ->From($userMail)

            // On attribue le destinataire
            ->To($dest)
            //->addTo('bar@example.com')

            // Copie au demandeur
            ->cc($copyDest)
            //->addCc('bar@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)

            // On crée le texte avec la vue
            //->text('Sending emails is fun again!')
            ->html($body);
        $this->mailer->send($message);
        }
        
    /**
     * sendTemplateMail Permet d'envoyer un mail avec une vue et des paramètres
     *
     * @param  string $userMail
     * @param  string $dest
     * @param  string $sujet
     * @param  string $template
     * @param  string $copy
     * @return void
     */
    Public function sendTemplateMail($userMail, $dest, $sujet, $template, $copyDest='postmaster.apamc@gmail.com'): void
    {
        $mail = (new TemplatedEmail())
        ->from($dest)
        ->to($userMail)
        ->cc($copyDest)
        ->subject($sujet)
        ->htmlTemplate($template)
        ->context([
            'firstname' => 'Joe'
        ])
        ;

        $this->mailer->send($mail);
    }
    
    /**
     * sendNotif Permet d'envoyer des notifications push sur différents canaux
     *
     * @param  string $message
     * @param  array $canal
     * @return void
     */
    public function sendNotif($message='Ceci est une notification d\'information', $canal=['browser'])
    {
        $this->notif->send(new Notification($message, $canal));
    }
}
