<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderLine;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Whishlist
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Cart
     */
    private $cart;

    public function __construct(
            SessionInterface $session,
            LoggerInterface $logger,
            Cart $cart
            ) {
        $this->session = $session;
        $this->logger = $logger;
        $this->cart = $cart;
    }

    /**
     * Add order line to wisheslist in session.
     */
    public function addToWishes(OrderLine $line): bool
    {
        $exists = false;
        $wishes = $this->getWishes();
        $wishes->addOrderLine($line);
        dump($wishes);
        foreach ($wishes->getOrderLines() as $line) {
            if ($line->getProduct()->getId() == $line->getProduct()->getId()) {
                $exists = true;
            }
        }
        if (!$exists) {
            $wishes->addOrderLine($line);
        }

        $this->updateWishes($wishes);

        return $exists;
    }

    /**
     * Add the order line from wishes list to cart.
     *
     * @return void
     */
    public function addToCart(OrderLine $line)
    {
        $this->cart->addToCart($line);
        $this->RemoveLine($line);
    }

    /**
     * Initialize the wisheslist in session if not already set.
     *
     * @return Order the wisheslist
     *
     * @throws \Exception
     */
    private function initialize(): Order
    {
        $wishes = null;
        if (false === $this->session->has('WISHES')) {
            $wishes = new Order();
            $wishes->setCreateAt(new \DateTime())
                ->setStatus(Order::ORDER_INITIATED)
                ->setUpdateAt(null);
            $this->session->set('WISHES', $wishes);
        } else {
            /**
             * @var Order $order
             */
            $wishes = $this->session->get('WISHES');
        }

        return $wishes;
    }

    /**
     * Update the cart stored in session.
     *
     * @return void
     */
    public function updateWishes(Order $wishes)
    {
        if (true === $this->session->has('WISHES')) {
            $this->session->set('WISHES', $wishes);
        }
    }

    /**
     * get the current ordder from the session or null if not set.
     */
    public function getWishes(): ?Order
    {
        $wishes = true === $this->session->has('WISHES') ? $this->session->get('WISHES') : $this->initialize();

        return $wishes;
    }

    /**
     * Remove orderline from wishesList.
     *
     * @return void
     */
    private function RemoveLine(OrderLine $line)
    {
        $wishes = $this->getWishes();
        $wishes->removeLine($line);
        $this->updateWishes($wishes);
    }
}
