<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use SessionIdInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CartController extends AbstractController
{
    protected $productRepository;
    protected $cartService;

    public function __construct(ProductRepository $productRepository, CartService $cartService)
    {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }



    #[Route('/cart/add/{id}', name: 'cart_add', requirements: ['id' => '\d+'])]
    public function add($id, FlashBagInterface $flashBag, Request $request): Response
    {
        //0. Securisation : Est ce que ce produit existe ?

        $product =  $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas !");
        }

        $this->cartService->add($id);

        $this->addFlash('success', "Le produit a bien été ajouté au panier");

        if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute("cart_show");
        }

        //$flashBag->add('success', "Le produit a bien été ajouté au panier");


        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);


        // return $this->render('cart/index.html.twig', [
        //     'controller_name' => 'CartController',
        // ]);
    }


    #[Route('/cart', name: 'cart_show')]
    public function show()
    {

        $detailedCart = $this->cartService->getDetailedCartItems();

        $total = $this->cartService->getTotal();

        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total
        ]);
    }

    #[Route('/cart/delete/{id}', name: 'cart_delete', requirements: ['id' => '\d+'])]

    public function delete($id)
    {

        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être supprimé !");
        }

        $this->cartService->remove($id);

        $this->addFlash('success', "Le produit a bien été supprimé au panier");

        return $this->redirectToRoute("cart_show");
    }


    #[Route('/cart/decrement/{id}', name: 'cart_decrement', requirements: ['id' => '\d+'])]
    public function decrement($id)
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut pas être décrémenté !");
        }

        $this->cartService->decrement($id);

        $this->addFlash('success', "Le produit a bien été décrémenté au panier");

        return $this->redirectToRoute("cart_show");
    }
}
