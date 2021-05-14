<?php

namespace App\Controller\Web;

use App\Form\SearchFormType;
use App\Repository\ApiTokenRepository;
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
    public function index(Request $request, ApiTokenRepository $apiTokenRepository, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if(!$this->getUser()->isVerified()){
            return $this->render('registration/verify_email.html.twig', [
                'error' => 'Please check your Emails and verify your Account first'
            ]);
        }

        $user = $userRepository->findByEmailField($this->getUser()->getUsername());
        $userId = $user->getId();

        if($token = $apiTokenRepository->findOneById($userId)){
            if($token->isExpired()){
                $token->renewExpiresAt();
            }
        }
        else{
            $apiTokenRepository->insertApiToken($this->getUser());
        }

        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        return $this->render('search/index.html.twig', [
            'search' => $form->createView(),
            'token' => $token->getToken()
        ]);
    }
}
