<?php

namespace App\Controller\Web;

use App\Form\MyDataForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MyDataController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    #[Route('/mydata', name: 'mydata')]
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->getUser()->isVerified()) {
            return $this->render('registration/verify_email.html.twig', [
                'errors' => 'Please check your Emails and verify your Account first'
            ]);
        }
        $authenticatedUser = $this->getUser();
        $form = $this->createForm(MyDataForm::class, $authenticatedUser);
        $user = $form->getData();
        $validator->validate($user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            $rPassword = $form->get('rPassword')->getData();
            if ($password && $rPassword) {
                if ($password === $rPassword) {
                    $user->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $password
                        )
                    );
                }
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->render('main/index.html.twig', [
                'main' => $form->createView(),
                'success' => "Update successfully",
                'errors' => null
            ]);
        }

        return $this->render('main/index.html.twig', [
            'main' => $form->createView(),
            'success' => null,
            'errors' => null
        ]);
    }
}
