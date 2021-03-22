<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\ContactType;
use App\Model\Contact;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\Cart;
use App\Service\Mailer;
use App\Service\Whishlist;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
    /**
     *  Displays the main page.
     *
     * @Route("/", name="default")
     * @Route("/home", name="home")
     *
     * @param ProductRepository $productRepo the repository
     *
     * @return Response the response instance
     */
    public function index(ProductRepository $productRepo): Response
    {
        $products = $productRepo->findAll();

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'products' => $products,
        ]);
    }

    /**
     * Gets the  list of brands.
     *
     * @return Response The  response instance
     */
    public function brands(): Response
    {
        return $this->render('default/brands.html.twig', [
            'brands' => [
                'Nike' => 20,
                'Puma' => 10,
                'Adidas' => 8,
            ],
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contactUs(): Response
    {
        return $this->render('default/contact.html.twig');
    }

    /**
     * Renders the menu.
     */
    public function menu(CategoryRepository $categoryRepo): Response
    {
        return $this->render('default/menu.html.twig', [
            'categories' => $categoryRepo->findAll(),
        ]);
    }

    /**
     * Retrieves the user menu : cart, login and register.
     *
     * @param Cart $cart the cart manager
     *
     * @return Response the response instance
     */
    public function user(Cart $cart, Whishlist $wishes): Response
    {
        return $this->render('default/user.html.twig', [
            'cart' => $cart->getCart(),
            'wishes' => $wishes->getWishes(),
        ]);
    }

    /**
     * Change the local language.
     *
     *@Route("/home/{local}", name="change_local")
     */
    public function channgeLocal(string $local, Request $request): Response
    {
        $request->getSession()->set('_locale', $local);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Displays the contact form.
     *
     * @Route("/contact-us", name="contactus")
     *
     * @return Response the response object
     */
    public function contact(Request $request, Mailer $mailer, TranslatorInterface $translator): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if (true === $form->isSubmitted()) {
            if (true === $form->isValid()) {
                $mailer->sendContactMail($contact);
                $message = $translator->trans('An email has been sent to the admin.');
                $this->addFlash('success', $message);

                return $this->redirectToRoute('default', [], Response::HTTP_FOUND);
            }
        }

        return $this->render('default/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays the search form.
     *
     * @return Response the response instance
     */
    public function searchForm(): Response
    {
        return $this->render('default/search_form.html.twig');
    }
}
