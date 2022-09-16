<?php

namespace App\Controller\Api\v1;

use App\Entity\Userlist;
use App\Repository\UserListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/v1/userlists", name="api_v1_userlists_")
 */
class UserlistController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(UserListRepository $userlistRepository): Response
    {
        $allUserlists = $userlistRepository->findAll();
        
        return $this->json($allUserlists, Response::HTTP_OK, [], ['groups' => 'api_userlists_browse']);
    }

    /**
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read(int $id, UserListRepository $userListRepository): Response
    {
        $userList = $userListRepository->find($id);

        return $this->json($userList, Response::HTTP_OK, [], ['groups' => 'api_userlists_read']);
    }

    /**
     * @Route("/{id}", name="edit", methods={"PATCH"}, requirements={"id"="\d+"})
     */
    public function edit(int $id, UserListRepository $userListRepository, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $userList = $userListRepository->find($id);

        $jsonContent = $request->getContent();
        $serializer->deserialize($jsonContent, Userlist::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $userList]);  

        $entityManager->persist($userList);
        $entityManager->flush();

        return $this->read($id, $userListRepository);
    }

    /**
     * @Route("", name="add", methods={"POST"})
     */
    public function add(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $jsonContent = $request->getContent();        
        
        $user = $serializer->deserialize($jsonContent, Userlist::class, 'json');
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
            'message' => 'User list created',
            'id' => $user->getId(),
            'series' => $user->getSeries(),
            'seasonNb' => $user->getSeasonNb(),
            'episodeNb' => $user->getEpisodeNb(),
            'type' => $user->getType(),
            'users' => $user->getUsers(),
        ];

        return $this->json($responseAsArray, Response::HTTP_CREATED, [], ['groups' => 'api_userlists_POST']);
    }
}
