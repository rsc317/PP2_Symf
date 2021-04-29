<?php

namespace App\Controller;

use App\Form\SearchFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class SearchController extends AbstractController
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    #[Route('/search', name: 'search')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        if ($this->isGranted('ROLE_USER') == false) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $values = $form->getData();
            if ($users = $userRepository->searchByValueFields($values)) {
                return $this->render('search/index.html.twig', [
                    'search' => $form->createView(),
                    'users' => $users,
                ]);
            }
        }
        return $this->render('search/index.html.twig', [
            'search' => $form->createView(),
            'users' => null,
        ]);
    }
}
