<?php

namespace App\Controller\Api\v1;

use App\Entity\Actor;
use App\Repository\ActorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/actors", name="api_v1_actors_")
 */
class ActorController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(ActorRepository $actorRepository): Response
    {
        $allActors = $actorRepository->findAll();
        
        
        return $this->json($allActors, Response::HTTP_OK, [], ['groups' => 'api_actors_browse']);
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read(int $id, ActorRepository $actorRepository): Response
    {
        $actor = $actorRepository->find($id);

        return $this->json($actor, Response::HTTP_OK, [], ['groups' => 'api_actors_read']);
    }
}
