<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PurchasePaymentSuccessController extends AbstractController
{

    #[Route('/purchase/terminate/{id}', name: 'purchase_payment_success')]
    public function success(
        $id,
        PurchaseRepository $purchaseRepository,
        EntityManagerInterface $em,
        CartService $cartService,
        EventDispatcherInterface $dispatcher
    ) {
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à vos commandes.');
        }

        //1. je recupère la commande
        $purchase = $purchaseRepository->find($id);

        if (
            !$purchase ||
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            $this->addFlash('warning', "La commande n'existe pas");

            return $this->redirectToRoute("purchase_index");
        }

        //2. Je la fais passer au status payé (PAID)
        $purchase->setStatus(Purchase::STATUS_PAID);
        $em->flush();

        //3. Je vide le panier
        $cartService->empty();

        // lancer un évènement qui permette aux autres développeurs de réagir à la prise d'une commande
        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        $dispatcher->dispatch($purchaseEvent, 'purchase.success');

        //4. Je redirige evec un flash vers la liste des commandes
        $this->addFlash('success', "La commande a été payée et confirmée !");
        return $this->redirectToRoute("purchase_index");
    }
}