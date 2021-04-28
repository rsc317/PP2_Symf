<?php

namespace App\Controller;

use App\Form\DetailsFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DetailsController extends AbstractController
{

    #[Route('/details/{email}', name: 'details')]
    public function index(Request $request, string $email, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $error = "";
        if($this->isGranted('ROLE_USER') == false) {
            return $this->redirectToRoute('app_login');
        }

        if($this->getUser()->getEmail() === $email){
            return $this->redirectToRoute('mydata');
        }

        if ($selectedUser = $userRepository->findByEmailField($email)) {
            $entityManager = $this->getDoctrine()->getManager();
            $form = $this->createForm(DetailsFormType::class, $selectedUser);
            $form->handleRequest($request);
            if ($form->getClickedButton() && 'update' ===  $form->getClickedButton()->getName()) {
                $plainPassword = $form->get('plainPassword')->getData();
                $repeatPassword = $form->get('rPlainPassword')->getData();
                if($plainPassword && $repeatPassword){
                    if($plainPassword === $repeatPassword) {
                        $user = $form->getData();
                        $user->setPassword(
                            $passwordEncoder->encodePassword(
                                $user,
                                $form->get('plainPassword')->getData()
                            )
                        );
                    }
                }
                if($roles = $form->get('roles')->getData()){
                  $selectedUser->setRoles([$roles]);
                }

                $entityManager->flush();
                $error = "Update successfully";
            }
            if ($form->getClickedButton() && 'delete' ===  $form->getClickedButton()->getName()) {
                $entityManager->remove($selectedUser);
                $entityManager->flush();
                return $this->redirectToRoute('list_persons');
            }

            return $this->render('details/index.html.twig', [
                'details' => $form->createView(),
                'user' => $selectedUser,
                'error' => $error,
            ]);
        }

        return $this->redirectToRoute('list_persons');
    }
}
