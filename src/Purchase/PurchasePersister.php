<?php

namespace App\Purchase;

use App\Cart\CartService;
use DateTimeImmutable;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $security;
    protected $cartService;
    protected $em;

    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }
    public function storePerchase(Purchase $purchase)
    {
        //Intégrer tout ce qu'il faut et persister la purchase ( la commande )

        //6. nous allons la lier avec l'utilisateur actuellemnt connecté (securité)
        $purchase->setUser($this->security->getUser())
            ->setPurchasedAt(new DateTimeImmutable())
            ->setTotal($this->cartService->getTotal());

        $this->em->persist($purchase);

        //7. Nous allons la lier avec les produits qui sont dans le panier (CartService)

        foreach ($this->cartService->getDetailedCartItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal())
                ->setProductPrice($cartItem->product->getPrice());

            $this->em->persist($purchaseItem);
        }
    }
}
