<?php

namespace App\Controller\Web;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws TransportExceptionInterface
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator): Response
    {
        if($this->isGranted('ROLE_USER') !== false) {
            return $this->redirectToRoute('mydata');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $plainPassword = $form->get('plainPassword')->getData();
            $repeatPassword = $form->get('repeatPassword')->getData();
            $user = $form->getData();
            if($plainPassword === $repeatPassword){
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $user->setRoles(['ROLE_USER']);
            }
            $validator->validate($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('phptestm01@gmail.com', 'Simple Web Application Bot'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView(),
                'success' => 'Registration successfull, please check your emails and verify your account!',
                'errors' => null
            ]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'success' => null,
            'errors' => null
        ]);
    }

    /**
     * @Route("verify/email/", name="app_verify_email")
     * @param Request $request
     * @return Response
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            return $this->render('registration/exception_email.html.twig', [
                'error' => $exception,
            ]);
        }

        return $this->redirectToRoute('mydata');
    }
}
