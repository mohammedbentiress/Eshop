<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Require ROLE_ADMIN for *every* controller method in this class.
 * 
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="dashboard")
     */
    public function dashboard(): Response
    {
        return $this->render('admin/index.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/login", name="login")
     */
    public function login(): Response
    {
        return $this->render('admin/login.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/register", name="register")
     */
    public function register(): Response
    {
        return $this->render('admin/register.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/reset-password", name="reset-password")
     */
    public function restPassword(): Response
    {
        return $this->render('admin/password.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/charts", name="charts")
     */
    public function charts(): Response
    {
        return $this->render('admin/charts.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/tables", name="tables")
     */
    public function tables(): Response
    {
        return $this->render('admin/tables.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/layout-sidenav-light", name="sidenav-light")
     */
    public function layout_sidenav(): Response
    {
        return $this->render('admin/layout-sidenav-light.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/layout-static", name="static")
     */
    public function layout_static(): Response
    {
        return $this->render('admin/layout-static.html.twig', [
        ]);
    }
}
