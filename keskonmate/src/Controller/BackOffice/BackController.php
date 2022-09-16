<?php

namespace App\Controller\BackOffice;

use App\Entity\Series;
use App\Form\HomeOrderType;
use App\Form\SeriesType;
use App\Repository\SeriesRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("", name="backoffice_") 
 */
class BackController extends AbstractController
{
    /**
     * @Route("", name="homepage", methods={"GET", "POST"})
     * 
     * @Security("is_granted('ROLE_CATALOGUE_MANAGER') or is_granted('ROLE_ADMIN')")
     */
    public function index(Request $request, SeriesRepository $seriesRepository, EntityManagerInterface $entityManager): Response
    {
        $seriesAll = $seriesRepository->findAllAlphaBetical();
        $seriesList = $seriesRepository->findAllByHomeOrder();
        $homeOrderForm = $this->createForm(HomeOrderType::class, $seriesList, [
            'disabled' => 'disabled'
        ]);

        $homeOrder = $request->get('homeOrder'); 

        $errors = [];

        if($homeOrder) {
            
            foreach($seriesList as $series) {
                $series->setHomeOrder(0);
            }
    
            foreach($homeOrder as $order => $seriesId) {
                $series = $seriesRepository->find(intval($seriesId));

                if($series) {
                    $series->setHomeOrder(intval($order));
                }
                else {
                    $errors[] = 'You must select 5 series';
                    break;
                }            
            }

            if(empty($errors)) {
                $entityManager->flush();
                return $this->redirectToRoute('backoffice_homepage');
            }
        }

       
        return $this->render('backoffice/homeorder/browse.html.twig', [
            'series_all' => $seriesAll,
            'series_list' => $seriesList,
            'series_form' => $homeOrderForm->createView(),
            'home_orders_count' => 5 - count($seriesList),
            'errors' => $errors        
        ]);
    }      

    /**
     * @Route("/{id}", name="edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * 
     * @Security("is_granted('ROLE_CATALOGUE_MANAGER') or is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Series $series): Response
    {
        $seriesForm = $this->createForm(SeriesType::class, $series);
        $seriesForm
            ->remove('createdAt')
            ->remove('updatedAt');

        $seriesForm->handleRequest($request);
        
        if ($seriesForm->isSubmitted() && $seriesForm->isValid()) {
            
            $entityManager = $this->getDoctrine()->getManager();
            
            $series->setUpdatedAt(new DateTimeImmutable());
            try{
                $entityManager->flush();
            } catch (Exception $e) {
                $this->addFlash('danger', "Cet emplacement est deja pris");
                return $this->redirectToRoute('backoffice_homepage');
            }

            $this->addFlash('success', "'{$series->getTitle()}' a ete ajoute a la liste");

            return $this->redirectToRoute('backoffice_homepage');
        }

        return $this->render('backoffice/series/add.html.twig', [
            'series_form' => $seriesForm->createView(),
            'series' => $series,
            'page' => 'edit',
        ]);
    }
}
