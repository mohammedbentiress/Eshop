<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Product;
use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class WhishListController extends AbstractController
{
    /**
     * @Route("/addToWishes/{id}", name="add_wishes")
     */
    public function AddToWishes(
        SessionInterface $session,
        Product $product,
        TranslatorInterface $translator
        ): Response {
        if (true === $session->has('WISHES')) {
            $wishes = $session->get('WISHES');
        } else {
            $wishes = new Order();
            $wishes->setCreateAt(new \DateTime())
            ->setStatus(Order::ORDER_INITIATED);
            $session->set('WISHES', $wishes);
        }
        $exists = false;
        $orderLine = new OrderLine();
        $orderLine->setQuantity(1);
        $orderLine->setProduct($product);
        foreach ($wishes->getOrderLines() as $line) {
            if ($line->getProduct()->getID() == $orderLine->getProduct()->getId()) {
                $exists = true;
            }
        }
        if (!$exists) {
            $wishes->addOrderLine($orderLine);
            $message = $translator->trans('Product is added to your wishes list, please check it out');
        } else {
            $message = $translator->trans('Product is already in your wishes list, please check it out');
        }

        $this->addFlash('success', $message);
        $session->set('WISHES', $wishes);

        return $this->redirectToRoute('product', ['slug' => $product->getSlug()]);
    }

    /**
     * @Route("/wishesList", name="wishesList")
     */
    public function wishesList(SessionInterface $session, Request $request): Response
    {
        $wishes = $session->get('WISHES');
        if (null == $wishes) {
            $wishes = new Order();
        }
        $form = $this->createForm(OrderType::class, $wishes);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('WISHES', $wishes);
        }

        return $this->render('wishList/wishes.html.twig', [
            'form' => $form->createView(),
            'cart' => $wishes,
        ]);
    }
}
