<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends AbstractController
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function renderMenuList()
    {

        $categories = $this->categoryRepository->findAll();

        return $this->render('category/_menu.html.twig', [
            'categories' => $categories
        ]);
    }

    #[Route('/{slug}', name: 'category_show')]

    public function show($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);
        //dd($category);
        if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }


    #[Route('/admin/category/{id}/edit', name: 'category_edit')]
    // #[IsGranted('ROLE_ADMIN', message: "Vous n'avez pas le droit d'acceder à cette  resource.")]
    public function edit(
        $id,
        CategoryRepository $categoryRepository,
        Request $request,
        SluggerInterface $slugger,
        EntityManagerInterface $em,
        Security $security
    ) {
        //$this->denyAccessUnlessGranted("ROLE_ADMIN", null, "Vous n'avez pas le droit d'accéder à cette resource !");
        // $user = $security->getUser();

        // if ($user === null) {
        //     return $this->redirectToRoute('app_login');
        // }

        // if (!in_array("ROLE_ADMIN", $user->getRoles())) {
        //     throw new AccessDeniedHttpException("Vous n'avez pas le droit d'accéder à cette resource !");
        // }

        $category = $categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundHttpException("Cette catégorie n'existe pas");
        }

        //$security->isGranted('CAN_EDIT', $category);

        $this->denyAccessUnlessGranted('CAN_EDIT', $category, "Vous n'êtes pas le propeiétaire de cette catégorie");

        // $user = $this->getUser();

        // if (!$user) {
        //     return $this->redirectToRoute('app_login');
        // }

        // if ($user !== $category->getOwner()) {
        //     throw new AccessDeniedHttpException("Vous n'êtes pas le propriétaire de cette catégory");
        // }

        $form = $this->createForm(CategoryType::class, $category);

        //$form->setData($product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));

            $em->flush();

            return $this->redirectToRoute('product_category', [
                'slug' => $category->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }

    #[Route('/admin/category/create', name: 'category_create')]
    public function create(
        Request $request,
        SluggerInterface $slugger,
        EntityManagerInterface $em
    ) {
        $category = new Category;

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setSlug(strtolower($slugger->slug($category->getName())));

            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();

        return $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
