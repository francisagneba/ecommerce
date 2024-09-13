<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{

    #[Route('/{category_slug}/{slug}', name: 'product_show', priority: -1)]
    public function show($slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['slug' => $slug]);

        if (!$product) {
            throw $this->createNotFoundException("Le produit démendé n'existe pas");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/admin/product/{id}/edit', name: 'product_edit')]
    public function edit(
        $id,
        ProductRepository $productRepository,
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ) {

        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);

        //$form->setData($product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // dd($form->getData());
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }

    #[Route('/admin/product/create', name: 'product_create')]
    public function create(
        Request $request,
        SluggerInterface $slugger,
        EntityManagerInterface $em
    ) {
        $product = new Product;

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $product->setSlug(strtolower($slugger->slug($product->getName())));

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
