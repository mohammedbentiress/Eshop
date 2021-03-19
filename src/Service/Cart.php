<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ProductRepository
     */
    private $repository;

    public function __construct(
            SessionInterface $session,
            EntityManagerInterface $manager,
            Mailer $mailer,
            ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->manager = $manager;
        $this->mailer = $mailer;
        $this->repository = $productRepository;
    }

    /**
     * Add order line to order in session.
     *
     * @return void
     */
    public function addToCart(OrderLine $line)
    {
        $order = $this->initialize();
        $order->addOrderLine($line);
        $this->session->set('CART', $order);
    }

    /**
     * Initialize the cart in session if not already set.
     *
     * @return Order the order
     *
     * @throws \Exception
     */
    private function initialize(): Order
    {
        $order = null;
        if (false === $this->session->has('CART')) {
            $order = new Order();
            $order->setCreateAt(new \DateTime())
                ->setStatus(Order::ORDER_INITIATED)
                ->setUpdateAt(null);
            $this->session->set('CART', $order);
        } else {
            /**
             * @var Order $order
             */
            $order = $this->session->get('CART');
        }

        return $order;
    }

    /**
     * update the cart stored in session.
     *
     * @return void
     */
    public function updateCart(Order $order)
    {
        if (true === $this->session->has('CART')) {
            $this->session->set('CART', $order);
        }
    }

    /**
     * get the current ordder from the session or null if not set.
     */
    public function getCart(): ?Order
    {
        $order = true === $this->session->has('CART') ? $this->session->get('CART') : $this->initialize();

        return $order;
    }

    /**
     * Save the order and orderlines to database.
     *
     * @param Order $cart cart instance
     *
     * @return void
     */
    public function saveToDatabase(Order $cart)
    {
        $cart = $this->getCart();
        $cart->setStatus(Order::ORDER_PLACED);
        $this->manager->persist($cart);
        foreach ($cart->getOrderLines() as $line) {
            $this->manageQuantity($line);
        }
        $this->manager->persist($cart);
        $this->manager->flush();
        $this->mailer->sendMail($cart);
        $this->clearCart();
        $this->logger->info('Order_Place', [
                'message' => 'Order placed success',
                'order' => $cart,
            ]);
    }

    /**
     * Deduct the ordered quantity from the product total quantity.
     *
     * @param Product $product  the product instance
     * @param int     $quantity the new quantity
     */
    private function updateProductQuantity(Product $product, int $quantity)
    {
        $product->setQuantity($product->getQuantity() - $quantity);
    }

    /**
     * Manages the quantity of the order line and the product.
     *
     * @param OrderLine $line the order line instance
     */
    private function manageQuantity(OrderLine $line)
    {
        /** @var Product $product */
        $product = $this->repository->find($line->getProduct());
        $line->setProduct($product);
        $this->manager->persist($line);
        $this->updateProductQuantity($product, $line->getQuantity());
        $this->manager->persist($product);
    }

    /**
     * Clears the session.
     *
     * @return void
     */
    private function clearcart()
    {
        $this->session->clear();
    }

    /**
     * @required
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
