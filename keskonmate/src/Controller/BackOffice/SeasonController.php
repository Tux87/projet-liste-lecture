<?php

namespace App\Controller\BackOffice;

use App\Entity\Season;
use App\Repository\SeasonRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/seasons", name="backoffice_seasons_") 
 */
class SeasonController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_CATALOGUE_MANAGER') or is_granted('ROLE_ADMIN')")
     */
    public function browse(SeasonRepository $seasonRepository): Response
    {
        return $this->render('backoffice/season/browse.html.twig', [
            'season_list' => $seasonRepository->findAll(),
        ]);
    }

    /**
     * @Route("/read/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     * 
     * @Security("is_granted('ROLE_CATALOGUE_MANAGER') or is_granted('ROLE_ADMIN')")
     */
    public function read(Request $request, $id, SeasonRepository $seasonRepository): Response
    {       
        $season = $seasonRepository->findOneWithInfosDQL($id); 

        $seasonForm = $this->createForm(SeasonType::class, $season, [
            'disabled' => 'disabled'
        ]);

        return $this->render('backoffice/season/read.html.twig', [
            'season_form' => $seasonForm->createView(),
            'season' => $seasonForm,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * 
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function edit(Request $request, Season $season): Response
    {
        $seasonForm = $this->createForm(GenreType::class, $season);
        $seasonForm
            ->remove('createdAt')
            ->remove('updatedAt');        

        $seasonForm->handleRequest($request);

        if ($seasonForm->isSubmitted() && $seasonForm->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            
            $season->setUpdatedAt(new DateTimeImmutable());
            $entityManager->flush();

            $this->addFlash('success', "La saison '{$season->getId()}' a été mis à jour");

            return $this->redirectToRoute('backoffice_season_browse');
        }

        return $this->render('backoffice/season/add.html.twig', [
            'season_form' => $seasonForm->createView(),
            'season' => $season,
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
        $season = new Season();
        $season->setCreatedAt(new DateTimeImmutable());
        $seasonForm = $this->createForm(GenreType::class, $season);
        $seasonForm
            ->remove('createdAt')
            ->remove('updatedAt');  
        $seasonForm->handleRequest($request);

        if ($seasonForm->isSubmitted() && $seasonForm->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();  
            $entityManager->persist($season);
            $entityManager->flush();

            // pour opquast 
            $this->addFlash('success', "La saison '{$season->getId()}' a été créé");

            // redirection
            return $this->redirectToRoute('backoffice_season_browse');
        }

        return $this->render('backoffice/season/add.html.twig', [
            'season_form' => $seasonForm->createView(),
            'page' => 'create',
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"GET"}, requirements={"id"="\d+"})
     * 
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")
     */
    public function delete(Season $season, EntityManagerInterface $entityManager): Response
    {
        $this->addFlash('success', "Season {$season->getId()} deleted");

        $entityManager->remove($season);
        $entityManager->flush();

        return $this->redirectToRoute('backoffice_season_browse');
    }
}
