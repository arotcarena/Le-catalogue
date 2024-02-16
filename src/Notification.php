<?php
namespace Vico;

use Vico\Config;
use Vico\Models\Invoice;
use Vico\TemplateEngine;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

class Notification
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var string
     */
    private $app_url;

    /**
     * @var string
     */
    private $app_send_mail;

    public function __construct()
    {
        $transport = Transport::fromDsn(Config::MAIL_HOST);
        $this->mailer = new Mailer($transport);
        $this->app_url = Config::APP_URL;
        $this->app_send_mail = Config::APP_SEND_MAIL;
    }


    public function welcomeEmail(string $email, string $link)
    {
        $link = $this->app_url . $link;

        $email = (new Email())
            ->from($this->app_send_mail)
            ->to($email)
            ->subject('Bienvenue sur Vector.com - Confirmez votre e-mail')
            ->text("Afin de finaliser votre inscription, veuillez copier-coller le lien suivant dans la barre de recherche de votre navigateur \n\n {$link}")
            ->html((new TemplateEngine('emails/welcome.php', ['link' => $link]))->view());
 
        $this->mailer->send($email);
    }

    public function init_passwordEmail(string $email, string $link)
    {
        $link = $this->app_url . $link;
        $email = (new Email())
            ->from($this->app_send_mail)
            ->to($email)
            ->subject('Vector.com - Réinitialisation du mot de passe')
            ->text("Cliquez sur ce lien pour réinitialiser votre mot de passe (validité 10 min) : \n\n {$link}")
            ->html((new TemplateEngine('emails/init_password.php', ['link' => $link]))->view());

        $this->mailer->send($email);
    }

    public function send2FA(string $email, int $code)
    {
        $email = (new Email())
            ->from($this->app_send_mail)
            ->to($email)
            ->subject('Vector.com - Authentification 2FA')
            ->text("Utilisez le code suivant pour vous connecter (validité 5 min) : {$code}")
            ->html((new TemplateEngine('emails/code_2FA.php', ['code' => $code]))->view());
            
        $this->mailer->send($email);
    }

    public function orderConfirm(string $email, string $link, Invoice $invoice)
    {
        $link = $this->app_url . $link;
        $email = (new Email())
            ->from($this->app_send_mail)
            ->to($email)
            ->subject($invoice->getEmailObject())
            ->text($invoice->toMail())
            ->html((new TemplateEngine('emails/order_confirm.php', ['link' => $link, 'invoice' => $invoice]))->view());
            //->attachFromPath('data/invoices/'.$invoice->getId().'.pdf');

        $this->mailer->send($email);
    }
    public function orderTracking(string $email, string $link, Invoice $invoice)
    {
        $link = $this->app_url . $link;
        $email = (new Email())
            ->from($this->app_send_mail)
            ->to($email)
            ->subject($invoice->getEmailObject())
            ->text($invoice->toMail())
            ->html((new TemplateEngine('emails/order_tracking.php', ['link' => $link, 'invoice' => $invoice]))->view());

        $this->mailer->send($email);
    }
}