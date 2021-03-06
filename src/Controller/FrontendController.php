<?php
declare(strict_types = 1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class FrontendController extends AbstractController
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('frontend/index.html.twig', [
            'controller_name' => 'FrontendController',
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function indexNoLocale(): RedirectResponse
    {
        return $this->redirectToRoute('index', [
            '_locale' => 'en'
        ]);
    }
}
