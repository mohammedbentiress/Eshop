<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Product;
use App\Entity\Shipping;
use App\Event\CartPlacedEvent;
use App\Form\CheckoutType;
use App\Form\OrderType;
use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CartController extends AbstractController
{
    /**
     *  Displays the cart page.
     *
     * @Route("/cart", name="cart")
     *
     * @param Cart    $cart    the cart manager
     * @param Request $request the request instance
     */
    public function cart(Cart $cart, Request $request, TranslatorInterface $translator): Response
    {
        $order = $cart->getCart();

        if (null == $order) {
            $order = new Order();
        }
        $form = $this->createForm(OrderType::class, $order);

        $form->handleRequest($request);

        if ($form->isSubmitted()
            && $form->isValid()) {
            $message = $translator->trans('Your cart has been updated.');

            $cart->updateCart($order);
            $this->addFlash('success', $message);

            return $this->redirectToRoute('cart', [], Response::HTTP_FOUND);
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $order,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Clears the cart.
     *
     * @Route("/cart/clear", name="clear")
     *
     * @param SessionInterface $session the current session to be cleared
     *
     * @return RedirectResponse the redirect response instance
     */
    public function clear(SessionInterface $session): RedirectResponse
    {
        $session->clear();

        return $this->redirectToRoute('default', [], Response::HTTP_FOUND);
    }

    /**
     * Add an orderline from wish list to cart.
     *
     * @Route("/wishesList/addTocart/{id}/{qt}", name = "wishes_add_cart")
     */
    public function wishesToCart(
        Product $product,
        int $qt,
        Cart $cart,
        TranslatorInterface $translator
        ): Response {
        $order = $cart->getCart();
        $orderLine = new OrderLine();
        $orderLine->setProduct($product)
                ->setQuantity($qt)
                ->setCart($order);
        $cart->addToCart($orderLine);

        $message = $translator->trans('Product is added to your cart, please check it out');
        $this->addFlash('success', $message);

        return $this->redirectToRoute('wishesList', [], Response::HTTP_FOUND);
    }

    /**
     * Displays the checkout page.
     *
     * @Route("/checkout", name="checkout")
     *
     * @return Response the response instance
     *
     * @throws CartNotValidException
     */
    public function checkout(
        Request $request,
        Cart $cart,
        TranslatorInterface $translator,
        EventDispatcherInterface $dispatcher
        ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $checkout = $cart->getCart();
        $shipping = new Shipping();
        if (null === $checkout) {
            throw $this->createNotFoundException('No cart was set.');
        } else {
            $checkout->setShipping($shipping);
        }
        $form = $this->createForm(CheckoutType::class, $shipping);
        $form->handleRequest($request);
        if ($form->isSubmitted()
            && $form->isValid()) {
            $cart->updateCart($checkout);
            $checkout->setUser($this->getUser());
            $cart->saveToDatabase($checkout);
            $message = $translator->trans('Thanks for using our website. Your cart has been submitted.');
            $this->addFlash('success', $message);

            $event = new CartPlacedEvent($checkout);

            $dispatcher->dispatch($event, CartPlacedEvent::NAME);

            return $this->redirectToRoute('default', [], Response::HTTP_FOUND);
        }

        return $this->render('cart/checkout.html.twig', [
            'form' => $form->createView(),
            'order' => $checkout,
        ]);
    }
}
