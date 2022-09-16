<?php

namespace App\Controller\Test;

use App\Entity\Genre;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test/test", name="test_test")
     */
    public function index(ObjectManager $entityManager): Response
    {
        $apiKey = "cf1a12ef45358291386bdd7c5689f02f";

        $listArray = [];
        $addedTvShowId = [];
        $response = [];
        for ($i=1; count($listArray) < 1; $i++) { 

            $r = rand(50,50);
            
            if(!in_array($r, $addedTvShowId)){
                try{
                    $response = (object) json_decode(file_get_contents("https://api.themoviedb.org/3/tv/$r?api_key=$apiKey&language=fr-FR"));
                }
                catch(\Exception $e){
                    array_push($listArray, [
                        'notAddedTvShow' => [
                            'tvShowId' => $r,
                            'error' => $e,
                        ],
                    ]);
                }
                
                /* dump($response->created_by[0]->name); */
                $arrayToDb = [
                    'requestUrl' => "https://api.themoviedb.org/3/tv/$r?api_key=$apiKey&language=fr-FR",
                    'tmdbTvShowId' => $r,
                    'tvShowDetails' => [
                        'title' => $response->name,
                        'synopsis' => $response->overview,
                        'releaseDate' => $response->first_air_date,
                        'image' => 'https://www.themoviedb.org/t/p/w1920_and_h800_multi_faces'.$response->backdrop_path,
                        'director' => $response->created_by[0]->name,
                        'numberOfSeasons' => $response->number_of_seasons,
                        'genre' => [],
                        'season' => [],
                    ],
                ];
                
                foreach($response->genres as $genresIndex => $genre){
                    array_push($arrayToDb['tvShowDetails']['genre'], "$genre->name");
                }
                
                /* foreach($response->seasons as $seasonIndex => $season){
                    array_push($arrayToDb['tvShowDetails']['season'], [
                        'series' => '',
                        ]);
                    } */
                    
                    
                    array_push($listArray, $arrayToDb);
                }
            
                array_push($addedTvShowId, $r);
            }
            

            $genres = (object) json_decode(file_get_contents("https://api.themoviedb.org/3/genre/tv/list?api_key=$apiKey&language=fr-FR"));
            
            foreach ($genres->genres as $k => $v) {
                $testing = new Genre();
                $entityManager->persist($testing);
            
                $testing->setName($v->name);
                
                $testing->setCreatedAt(new DateTimeImmutable('2021-10-28'));

                $entityManager->flush();
            } 
            dd();


            return $this->json($listArray);
        }
    }
