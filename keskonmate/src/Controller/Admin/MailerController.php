<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;


class MailerController extends AbstractController
{
    private $verifyEmailHelper;
    
    public function __construct(VerifyEmailHelperInterface $helper)
    {
        $this->verifyEmailHelper = $helper;
    }    

    /**
     * @Route("/verify", name="registration_confirmation_route")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    { 
        $id = $request->get('id'); // retrieve the user id from the url
 
        // Verify the user id exists and is not null
        if (null === $id) {
            return $this->redirectToRoute('nouser');
        }
 
        $user = $userRepository->find($id);
 
        // Ensure the user exists in persistence
        if (null === $user) {
            return $this->redirectToRoute('nouserpers');
        }
        

        // Do not get the User's Id or Email Address from the Request object
        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('danger', $e->getReason());
            return $this->redirectToRoute('login');
        }
    
        $user->setVerified(1);
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', 'Your e-mail address has been verified, please log in.');

        return $this->redirect('http://keskonmate.surge.sh/');
    }  
}
