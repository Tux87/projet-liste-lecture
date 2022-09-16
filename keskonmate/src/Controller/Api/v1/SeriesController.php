<?php

namespace App\Controller\Api\v1;

use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/series", name="api_v1_series_")
 */
class SeriesController extends AbstractController
{
    /**
     * @Route("", name="browse", methods={"GET"})
     */
    public function browse(SeriesRepository $seriesRepository, Request $request): Response
    {
        $column = $request->get('column');
        $genre = $request->get('genre');
        $order = $request->get('order');        
        $keyword = $request->get('keyword');

        if($genre || $order || $column || $keyword) {
            $allSeries = $seriesRepository->findAllByFilters($column, $genre, $order, $keyword);
        } else {
            $allSeries = $seriesRepository->findAll();
        }
       
        return $this->json($allSeries, Response::HTTP_OK, [], ['groups' => 'api_series_browse']);
    }    

    /**
     * @Route("/{id}", name="read", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function read(int $id, SeriesRepository $seriesRepository): Response
    {
        $series = $seriesRepository->find($id);

        return $this->json($series, Response::HTTP_OK, [], ['groups' => 'api_series_read']);
    }

    /**
     * @Route("/homeorder", name="homeorder", methods={"GET"})
     */
    public function homeOrder(SeriesRepository $seriesRepository): Response
    {
        $series = $seriesRepository->findAllByHomeOrder();

        return $this->json($series, Response::HTTP_OK, [], ['groups' => 'api_series_read']);
    }

}

