<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\OrderLine;
use App\Entity\Product;
use App\Form\AddToCartType;
use App\Repository\ProductRepository;
use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductController extends AbstractController
{
    /**
     * Display details of a given product using the slug.
     *
     * @Route("/product/{slug}", name="product")
     *
     * @param Product           $product     the product instance
     * @param Request           $request     the request instance
     * @param ProductRepository $productRepo the repository instance
     *
     * @return Response The response instance
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function showProduct(
        ProductRepository $productRepo,
        Request $request,
        Product $product,
        Cart $cart,
        TranslatorInterface $translator
        ): Response {
        $orderLine = new OrderLine();
        $orderLine->setQuantity(1);
        $orderLine->setProduct($product);
        $order = $cart->getCart();
        $form = $this->createForm(AddToCartType::class, $orderLine);

        $form->handleRequest($request);
        $exists = false;
        $found = -1;
        $message = $translator->trans('Product added to cart');

        if ($form->isSubmitted()
            && $form->isValid()) {
            foreach ($order->getOrderLines() as $line) {
                if ($line->getProduct()->getID() == $orderLine->getProduct()->getId()) {
                    $exists = true;
                    $sum = intval($orderLine->getQuantity()) + intval($line->getQuantity());
                    if ($sum > $product->getQuantity()) {
                        return $this->redirectToRoute('product_detail', [
                            'slug' => $product->getSlug(),
                            'errors' => 'The quantity has exceeded the max in the store',
                            ]);
                    }
                    $line->setQuantity($sum);
                    break;
                }
            }
            if (!$exists && -1 == $found) {
                $cart->addToCart($orderLine);
            }
            $cart->updateCart($order);
            $this->addFlash('success', $message);

            return $this->redirectToRoute('cart');
        }
        $related = $productRepo->getRelatedProducts($product);

        return $this->render('product/index.html.twig', [
            'product' => $product,
            'related' => $related,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Performs the search of the products bya given word.
     *
     * @Route("/search", name="search")
     *
     * @param Request           $request    the request instance
     * @param ProductRepository $repository the repository instance
     *
     * @return Response the response instance
     */
    public function search(Request $request, ProductRepository $repository): Response
    {
        $q = $request->query->get('q');
        $products = $repository->search([
            'term' => $q,
        ]);

        return $this->render('default/index.html.twig', [
            'products' => $products,
        ]);
    }
}
