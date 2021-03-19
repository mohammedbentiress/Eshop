<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * Gets all product of the given category.
     *
     * @Route("/category/{slug}", name="category")
     *
     * @param Category $category the repository instance
     *
     * @return Response the response instance
     */
    public function showCategory(Category $category): Response
    {
        return $this->render('category/index.html.twig', [
            'products' => $category->getProducts(),
        ]);
    }

    /**
     * Listing categories with count products for each.
     *
     * @param CategoryRepository $repository The repository instance
     *
     * @return Response The response instance
     */
    public function countByCategory(CategoryRepository $repository): Response
    {
        return $this->render('category/list.html.twig', [
            'categories' => $repository->findAll(),
        ]);
    }
}
