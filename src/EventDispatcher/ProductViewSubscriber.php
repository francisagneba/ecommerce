<?php

namespace App\EventDispatcher;

use App\Event\ProductViewEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'product.view' => 'sendEmail'
        ];
    }

    public function sendEmail(ProductViewEvent $productViewEvent)
    {
        $this->logger->info("Email envoyé à l'admin pour le produit n° " . $productViewEvent->getProduct()->getId());
    }
}