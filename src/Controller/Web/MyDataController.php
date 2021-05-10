<?php

namespace App\Controller\Web;

use App\Form\MyDataForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MyDataController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    #[Route('/mydata', name: 'mydata')]
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if(!$this->getUser()->isVerified()){
            return $this->render('registration/verify_email.html.twig', [
                'error' => 'Please check your Emails and verify your Account first'
            ]);
        }

        $authenticatedUser = $this->getUser();
        $error = '';
        $form = $this->createForm(MyDataForm::class, $authenticatedUser);
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
            $error = "Update successfully";
        }

        return $this->render('main/index.html.twig', [
            'main' => $form->createView(),
            'error' => $error,
        ]);
    }
}
