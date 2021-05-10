<?php

namespace App\Controller\Web;

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

    /**
     * @Route("/search", name="search")
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('/search', name: 'search')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if(!$this->getUser()->isVerified()){
            return $this->render('registration/verify_email.html.twig', [
                'error' => 'Please check your Emails and verify your Account first'
            ]);
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
