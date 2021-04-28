<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ListPersonsController extends AbstractController
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    #[Route('/list/persons', name: 'list_persons')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        if($this->isGranted('ROLE_USER') == false) {
            return $this->redirectToRoute('app_login');
        }

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $userRepository->getUsersPaginator($offset);

        return new Response($this->twig->render('list_persons/index.html.twig', [
            'rop' => UserRepository::PAGINATOR_PER_PAGE,
            'users' => $paginator,
            'offset' => $offset,
            'previous' => $offset - UserRepository::PAGINATOR_PER_PAGE,
            'next' => min(count($paginator), $offset + UserRepository::PAGINATOR_PER_PAGE),
        ]));
    }
}
