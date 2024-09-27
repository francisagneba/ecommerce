<?php

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use App\Event\PurchaseSuccessEvent;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;
    protected $mailer;
    protected $security;

    public function __construct(LoggerInterface $logger, MailerInterface $mailer, Security $security)
    {
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->security = $security;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'purchase.success' => 'sendSuccessEmail',
        ];
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        //1. Récuperer l'utilisateur en ligne pour connaitre son adresse
        //Je le recupère avec le service Security
        /**@var User */
        $currentUser = $this->security->getUser();

        //2. Récuperer la commande (je la trouve dans PruchaseSuccessEvent)
        $purchase = $purchaseSuccessEvent->getPurchase();

        //3. Ecrire le mail (nouveau TempletedEmail)
        $email = new TemplatedEmail();
        $email->to(new Address($currentUser->getEmail(), $currentUser->getFullName()))
            ->from('contact@mail.com')
            ->subject("Bravo, votre commande ({$purchase->getId()}) a bien été confirmée")
            ->htmlTemplate('emails/purchase_success.html.twig')
            ->context([
                'purchase' => $purchase,
                'user' => $currentUser
            ]);

        //4. Envoyer l'email on utilise le service Mailerinterface
        $this->mailer->send($email);

        $this->logger->info("Email envoyé pour la commande n° " . $purchaseSuccessEvent->getPurchase()->getId());
    }
}
