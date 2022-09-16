<?php

namespace App\Controller\Api\v1;

use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/genres", name="api_v1_genres_")
 */
class GenreController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(GenreRepository $genreRepository): Response
    {
        $allGenres = $genreRepository->findAll();
        // dd($allActors);
        
        return $this->json($allGenres, Response::HTTP_OK, [], ['groups' => 'api_genres_browse']);
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read(int $id, GenreRepository $genreRepository): Response
    {
        $genre = $genreRepository->find($id);

        return $this->json($genre, Response::HTTP_OK, [], ['groups' => 'api_genres_read']);
    }
}
