<?php

namespace App\Controller\BackOffice;

use App\Entity\Genre;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/genres", name="backoffice_genres_") 
 */
class GenreController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_CATALOGUE_MANAGER') or is_granted('ROLE_ADMIN')")
     */
    public function browse(GenreRepository $genreRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $data = $genreRepository->findBy([],['name' => 'asc']);

        $genres = $paginator->paginate(
            $data, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('backoffice/genre/browse.html.twig', [
            'genres' => $genres,
        ]);
    }    

    /**
     * @Route("/read/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     * 
     * @Security("is_granted('ROLE_CATALOGUE_MANAGER') or is_granted('ROLE_ADMIN')")
     */
    public function read(Request $request, $id, GenreRepository $genreRepository): Response
    {       
        $genre = $genreRepository->find($id); 

        $genreForm = $this->createForm(GenreType::class, $genre, [
            'disabled' => 'disabled'
        ]);

        return $this->render('backoffice/genre/read.html.twig', [
            'genre_form' => $genreForm->createView(),
            'genre' => $genre,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * 
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function edit(Request $request, Genre $genre): Response
    {
        $genreForm = $this->createForm(GenreType::class, $genre);
        $genreForm
            ->remove('createdAt')
            ->remove('updatedAt');

        $genreForm->handleRequest($request);

        if ($genreForm->isSubmitted() && $genreForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $genre->setUpdatedAt(new DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', "'{$genre->getName()}' a été mis à jour");

            return $this->redirectToRoute('backoffice_genres_browse');
        }

        return $this->render('backoffice/genre/add.html.twig', [
            'genre_form' => $genreForm->createView(),
            'genre' => $genre,
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
        $genre = new Genre();
        $genre->setCreatedAt(new DateTimeImmutable());
        $genreForm = $this->createForm(GenreType::class, $genre);
        $genreForm
            ->remove('createdAt')
            ->remove('updatedAt');
        $genreForm->handleRequest($request);

        if ($genreForm->isSubmitted() && $genreForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();  
            $entityManager->persist($genre);
            $entityManager->flush();

            // pour opquast 
            $this->addFlash('success', "'{$genre->getName()}' a été créé");

            // redirection
            return $this->redirectToRoute('backoffice_actors_browse');
        }

        return $this->render('backoffice/genre/add.html.twig', [
            'genre_form' => $genreForm->createView(),
            'page' => 'create',
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"GET"}, requirements={"id"="\d+"})
     * 
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function delete(Genre $genre, EntityManagerInterface $entityManager): Response
    {
        $this->addFlash('success', "Genre {$genre->getName()} deleted");

        $entityManager->remove($genre);
        $entityManager->flush();

        return $this->redirectToRoute('backoffice_genres_browse');
    }
}
