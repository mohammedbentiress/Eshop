<?php

namespace App\Controller;

use App\Entity\OrderLine;
use App\Entity\Product;
use App\Form\OrderType;
use App\Service\Whishlist;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class WhishListController extends AbstractController
{
    /**
     * @Route("/addToWishes/{id}", name="add_wishes")
     */
    public function AddToWishes(
        Product $product,
        TranslatorInterface $translator,
        Whishlist $wishes
        ): Response {
        $orderLine = new OrderLine();
        $orderLine->setQuantity(1);
        $orderLine->setProduct($product);
        dump($product);
        $exists = $wishes->addToWishes($orderLine);
        if (!$exists) {
            $message = $translator->trans('Product is added to your wishes list, please check it out');
        } else {
            $message = $translator->trans('Product is already in your wishes list, please check it out');
        }

        $this->addFlash('success', $message);

        return $this->redirectToRoute('product', ['slug' => $product->getSlug()]);
    }

    /**
     * @Route("/wishesList", name="wishesList")
     */
    public function wishesList(Request $request, Whishlist $wishes): Response
    {
        $wishesList = $wishes->getWishes();

        $form = $this->createForm(OrderType::class, $wishesList);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $wishes->updateWishes($wishesList);
        }

        return $this->render('wishList/wishes.html.twig', [
            'form' => $form->createView(),
            'cart' => $wishesList,
        ]);
    }

    /**
     * Add an orderline from wish list to cart.
     *
     * @Route("/wishesList/addTocart/{id}/{qt}", name = "wishes_add_cart")
     */
    public function wishesToCart(
        Product $product,
        int $qt,
        TranslatorInterface $translator,
        Whishlist $wishes
        ): Response {
        $orderLine = new OrderLine();
        $orderLine->setProduct($product)
                ->setQuantity($qt);
        $wishes->addToCart($orderLine);

        $message = $translator->trans('Product is added to your cart, please check it out');
        $this->addFlash('success', $message);

        return $this->redirectToRoute('wishesList', [], Response::HTTP_FOUND);
    }
}
