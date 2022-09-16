<?php

namespace App\Controller\Api\v1;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1/users", name="api_v1_users_")
 */
class UserController extends AbstractController
{
    private $verifyEmailHelper;
    private $mailer;
    
    public function __construct(VerifyEmailHelperInterface $helper, MailerInterface $mailer)
    {
        $this->verifyEmailHelper = $helper;
        $this->mailer = $mailer;
    }

    /**
     * @Route("", name="browse", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function browse(UserRepository $userRepository): Response
    {
        $allUsers = $userRepository->findAll();
        // dd($allUsers);        
        
        return $this->json($allUsers, Response::HTTP_OK, [], ['groups' => 'api_users_browse']);
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        
        return $this->json($user, Response::HTTP_OK, [], ['groups' => 'api_users_read']);
    }

    /**
     * @Route("/{id}", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(int $id, UserRepository $userRepository, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $userRepository->find($id);

        if (is_null($userRepository)) {
            return $this->getNotFoundResponse();
        }
        
        $jsonContent = $request->getContent();
        $serializer->deserialize($jsonContent, User::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $user]);   
        $errors = $validator->validate($user);

        
        if (! empty($request->toArray()['password'])) {
            $clearPassword = $request->toArray()['password'];
            $hashedPassword = $passwordHasher->hashPassword($user, $clearPassword);
            
            $user->setPassword($hashedPassword);
        }
        $errors = $validator->validate($user);
        
        if(count($errors) > 0) {
            $reponseAsArray = [
                'error' => true,
                'message' => $errors,
            ];
            
            return $this->json($reponseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        $user->setUpdatedAt(new DateTimeImmutable());        
        $entityManager->persist($user);
        $entityManager->flush();
        $responseAsArray = [
             'message' => 'Utilisateur mis a jour',
             'id' => $user->getId(),
        ];
 
         return $this->json($responseAsArray, Response::HTTP_CREATED);
    }

    /**
     * @Route("/add", name="add", methods={"POST"})
     */
    public function add(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $role = ["ROLE_USER"];

        $jsonContent = $request->getContent();
        $jsonContent = json_decode($jsonContent);

        // $id =  $jsonContent->userNickname;
        // $email = $jsonContent->email;
        
        $user->setEmail($jsonContent->email);
        $user->setRoles($role);
        $user->setUserNickname($jsonContent->userNickname);
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setVerified(0);       
        
        $clearPassword = $jsonContent->password;
        if (! empty($clearPassword)) {
            $hashedPassword = $passwordHasher->hashPassword($user, $clearPassword);
            $user->setPassword($hashedPassword);
        }        

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $responseAsArray = [
                'error' => true,
                'message' => $errors,
            ];
            return $this->json($responseAsArray, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $entityManager->persist($user);
        $entityManager->flush();
        $responseAsArray = [
            'message' => 'User created',
            'id' => $user->getId(),
        ];
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'registration_confirmation_route',
            $user->getId(),            
            $user->getEmail(),
            ['id' => $user->getId()] // add the user's id as an extra query param
        );

        $email = new TemplatedEmail();
        $email->from('keskonmate@gmail.com');
        $email->to($user->getEmail());
        $email->subject('Bienvenue chez Keskonmate!');
        $email->htmlTemplate('admin/registration/confirmation_email.html.twig');
        $email->context(['signedUrl' => $signatureComponents->getSignedUrl()]);
        
        $this->mailer->send($email);
        
        return $this->json($responseAsArray, Response::HTTP_CREATED);
        
    }

     /**
     * @Route("/{id}", name="delete", methods={"DELETE"}, requirements={"id"="\d+"})
     */
 /*   public function delete(int $id, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->find($id);

        if (is_null($user)) {
            return $this->getNotFoundResponse();
        }
        $entityManager->remove($user);
        $entityManager->flush();
        
        $reponseAsArray = [
            'message' => 'User supprimé',
            'id' => $id
        ];

        return $this->json($reponseAsArray);
    } */

    private function getNotFoundResponse() {

        $responseArray = [
            'error' => true,
            'userMessage' => 'Ressource non trouvé',
            'internalMessage' => 'Cet utilisateur n\'existe pas dans la BDD',
        ];

        return $this->json($responseArray, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}


// $signatureComponents = $this->verifyEmailHelper->generateSignature(
//     'registration_confirmation_route',
//     $id,
//     $email,
//     ['userNickname' => $user->getUserNickname()] // add the user's id as an extra query param            
// );