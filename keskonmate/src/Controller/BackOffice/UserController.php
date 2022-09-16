<?php

namespace App\Controller\BackOffice;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/users", name="backoffice_users_") 
 * 
 * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function browse(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $data = $userRepository->findBy([],['email' => 'asc']);
        $users = $paginator->paginate(
            $data, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('backoffice/user/browse.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/read/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     * 
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function read(Request $request, $id, UserRepository $userRepository): Response
    {       
        $user = $userRepository->find($id); 

        $userForm = $this->createForm(UserType::class, $user, [
            'disabled' => 'disabled'
        ]);

        return $this->render('backoffice/user/read.html.twig', [
            'user_form' => $userForm->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * 
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function edit(Request $request, User $user, UserPasswordHasherInterface $passwordHasher): Response
    {
        $userForm = $this->createForm(UserType::class, $user);
        $userForm
            ->remove('createdAt')
            ->remove('updatedAt');

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $clearPassword = $request->request->get('user')['password']['first'];
            if (! empty($clearPassword))
            {
                $hashedPassword = $passwordHasher->hashPassword($user, $clearPassword);
                $user->setPassword($hashedPassword);
            }
            
            $user->setUpdatedAt(new DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', "'{$user->getUserIdentifier()}' a été mis à jour");

            return $this->redirectToRoute('backoffice_users_browse');
        }

        return $this->render('backoffice/user/add.html.twig', [
            'user_form' => $userForm->createView(),
            'user' => $user,
            'page' => 'edit',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET", "POST"})
     * 
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function add(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setVerified(0);
        
        $userForm = $this->createForm(UserType::class, $user);
        $userForm
            ->remove('createdAt')
            ->remove('updatedAt');
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);

            $clearPassword = $request->request->get('user')['password']['first'];
            if (! empty($clearPassword))
            {
                $hashedPassword = $passwordHasher->hashPassword($user, $clearPassword);
                $user->setPassword($hashedPassword);
            }
            $entityManager->flush();

            $this->addFlash('success', "'{$user->getUserIdentifier()}' a été créé");

            return $this->redirectToRoute('backoffice_users_browse');
        }

        return $this->render('backoffice/user/add.html.twig', [
            'user_form' => $userForm->createView(),
            'page' => 'create',
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"GET"}, requirements={"id"="\d+"})
     * 
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    // public function delete(User $user, EntityManagerInterface $entityManager): Response
    // {
    //     $this->addFlash('success', "User {$user->getUserIdentifier()} deleted");

    //     $entityManager->remove($user);
    //     $entityManager->flush();

    //     return $this->redirectToRoute('backoffice_users_browse');
    // }
}
