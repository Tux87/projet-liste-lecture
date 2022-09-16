<?php

namespace App\Controller\BackOffice;

use App\Entity\Actor;
use App\Form\ActorType;
use App\Repository\ActorRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/actors", name="backoffice_actors_") 
 */
class ActorController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_CATALOGUE_MANAGER') or is_granted('ROLE_ADMIN')")
     */
    public function browse(ActorRepository $actorRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $data = $actorRepository->findBy([],['name' => 'asc']);
        $actors = $paginator->paginate(
            $data, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        $formSearchBar = $this->createFormBuilder()
            ->setAction($this->generateUrl('backoffice_actors_handleSearch'))
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Rechercher un acteur'
                ]
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();

        return $this->render('backoffice/actor/browse.html.twig', [
            'actors' => $actors,
            'searchBar' => $formSearchBar->createView()
        ]);
    }   

    /**
     * @Route("/read/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     * 
     * @Security("is_granted('ROLE_CATALOGUE_MANAGER') or is_granted('ROLE_ADMIN')")
     */
    public function read(Request $request, $id, ActorRepository $actorRepository): Response
    {       
        $actor = $actorRepository->find($id); 

        $actorForm = $this->createForm(ActorType::class, $actor, [
            'disabled' => 'disabled'
        ]);

        return $this->render('backoffice/actor/read.html.twig', [
            'actor_form' => $actorForm->createView(),
            'actor' => $actor,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * 
     *@Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function edit(Request $request, Actor $actor): Response
    {
        $actorForm = $this->createForm(ActorType::class, $actor);
        $actorForm
            ->remove('createdAt')
            ->remove('updatedAt');
        $actorForm->handleRequest($request);

        if ($actorForm->isSubmitted() && $actorForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $actor->setUpdatedAt(new DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', "'{$actor->getName()}' a été mis à jour");

            return $this->redirectToRoute('backoffice_actors_browse');
        }

        return $this->render('backoffice/actor/add.html.twig', [
            'actor_form' => $actorForm->createView(),
            'actor' => $actor,
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
        $actor = new Actor();
        $actor->setCreatedAt(new DateTimeImmutable());

        $actorForm = $this->createForm(ActorType::class, $actor);
        $actorForm
            ->remove('createdAt')
            ->remove('updatedAt');
        $actorForm->handleRequest($request);

        if ($actorForm->isSubmitted() && $actorForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();  
            $entityManager->persist($actor);
            $entityManager->flush();

            // pour opquast 
            $this->addFlash('success', "'{$actor->getName()}' a été créé");

            // redirection
            return $this->redirectToRoute('backoffice_actors_browse');
        }

        return $this->render('backoffice/actor/add.html.twig', [
            'actor_form' => $actorForm->createView(),
            'page' => 'create',
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"GET"}, requirements={"id"="\d+"})
     * 
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function delete(Actor $actor, EntityManagerInterface $entityManager): Response
    {
        $this->addFlash('success', "Actor {$actor->getId()} deleted");

        $entityManager->remove($actor);
        $entityManager->flush();

        return $this->redirectToRoute('backoffice_actors_browse');
    }
    
    /**
     * @Route("/handleSearch", name="handleSearch")
     * @param Request $request
     */
    public function handleSearch(Request $request, ActorRepository $actorRepository)
    {
        $query = $request->request->get('form')['query'];
        if($query) {
            $actor = $actorRepository->findActorByName($query);
        }
        return $this->render('backoffice/actor/_searchBar.html.twig', [
            'actors' => $actor
        ]);
    }
}
