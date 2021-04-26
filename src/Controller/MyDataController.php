<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\MainType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MyDataController extends AbstractController
{
    #[Route('/mydata', name: 'mydata')]
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $authenticated_user = $this->getUser();
        $form = $this->createForm(MainType::class, $authenticated_user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $repeatPassword = $form->get('rPlainPassword')->getData();
            if($plainPassword && $repeatPassword){
                if($plainPassword === $repeatPassword){
                    $user = $form->getData();
                    $user->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }
        return $this->render('main/index.html.twig', [
            'main' => $form->createView(),
        ]);
    }
}
