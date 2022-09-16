<?php

namespace App\Controller\BackOffice;

use App\Entity\Series;
use App\Entity\User;
use App\Entity\UserList;
use App\Form\UserlistType;
use App\Repository\UserListRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/userlists", name="backoffice_userlists_") 
 * 
 * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
 */
class UserlistController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function browse(UserListRepository $userlistRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $data = $userlistRepository->findBy([],['id' => 'asc']);

        $userlists = $paginator->paginate(
            $data, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            20 // Nombre de résultats par page
        );

        return $this->render('backoffice/userlist/browse.html.twig', [
            'userlists' => $userlists
        ]);
    }

    /**
     * @Route("/read/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     * 
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function read(Request $request, $id, UserListRepository $userlistRepository): Response
    {       
        $userlist = $userlistRepository->find($id);

        $userlistForm = $this->createForm(UserlistType::class, $userlist, [
            'disabled' => 'disabled'
        ]);

        return $this->render('backoffice/userlist/read.html.twig', [
            'userlist_form' => $userlistForm->createView(),
            'userlist' => $userlist,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * 
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function edit(Request $request, UserList $userlist): Response
    {
        $userlistForm = $this->createForm(UserlistType::class, $userlist);

        $userlistForm
            ->remove('createdAt')
            ->remove('updatedAt');

        $userlistForm->handleRequest($request);

        if ($userlistForm->isSubmitted() && $userlistForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $userlist->setUpdatedAt(new DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', "La liste `{$userlist->getId()}` a été mis à jour");

            return $this->redirectToRoute('backoffice_userlists_browse');
        }

        return $this->render('backoffice/userlist/add.html.twig', [
            'userlist_form' => $userlistForm->createView(),
            'userlist' => $userlist,
            'page' => 'edit',
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET", "POST"})
     * 
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userlist = new Userlist();
        
        $userlistForm = $this->createForm(UserlistType::class, $userlist);
        
        $userlistForm
            ->remove('createdAt')
            ->remove('updatedAt');

        $userlistForm->handleRequest($request);
        
        if ($userlistForm->isSubmitted() && $userlistForm->isValid()) {
          
            $entityManager = $this->getDoctrine()->getManager();  
            $entityManager->persist($userlist);
            $userlist->setCreatedAt(new DateTimeImmutable());
            $entityManager->flush();

            // pour opquast 
            $this->addFlash('success', "La liste '{$userlist->getId()}' a été créé");

            // redirection
            return $this->redirectToRoute('backoffice_userlists_browse');
        }

        return $this->render('backoffice/userlist/add.html.twig', [
            'userlist_form' => $userlistForm->createView(),
            'page' => 'create',
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"GET"}, requirements={"id"="\d+"})
     * 
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function delete(UserList $userlist, EntityManagerInterface $entityManager): Response
    {
        $this->addFlash('success', "La liste '{$userlist->getId()}' a été effacée");

        $entityManager->remove($userlist);
        $entityManager->flush();

        return $this->redirectToRoute('backoffice_userlists_browse');
    }
}
