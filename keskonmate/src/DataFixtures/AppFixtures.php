<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use App\Entity\Genre;
use App\Entity\Season;
use App\Entity\Series;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Exception;

class AppFixtures extends Fixture implements FixtureInterface
{
    public const GENRE_ID = "genre-";
    public const ACTOR_ID = "actor-";
    public const SEASON_ID = "season-";
    
    public function load(ObjectManager $entityManager): void
    {
        $apiKey = "84c05081ccc468ff6d3235adc25d38b7";

        $currentDateTime = new DateTimeImmutable(date("H:i:s"));
        
        $addedSeriesId = [];

        $tmdbGenreList = (object) json_decode(file_get_contents("https://api.themoviedb.org/3/genre/tv/list?api_key=$apiKey&language=fr-FR"));
        
        foreach ($tmdbGenreList->genres as $k => $genreInfo) {
            $genre = new Genre();
            $entityManager->persist($genre);
        
            $genre->setName($genreInfo->name);
            
            $genre->setCreatedAt($currentDateTime);

            $this->addReference(self::GENRE_ID."$genreInfo->id", $genre);
        }

        //genre "aucun genre" pour les série qui n'en n'ont pas
        $noGenre = new Genre();
        $entityManager->persist($noGenre);
        $noGenre->setName("Aucun Genre");
        $noGenre->setCreatedAt($currentDateTime);
        $this->addReference(self::GENRE_ID."-none", $noGenre);

        for ($i=1; count($addedSeriesId) < 50; $i++) {

            $r = rand(1, 1500); 
            
            $lorem = file_get_contents("https://loripsum.net/api/1/short/plaintext");
             
/*             while(in_array($r, $addedSeriesId) || empty($seriesApiResponse->overview) || empty($seriesApiResponse->backdrop_path) || !isset($seriesApiResponse->created_by[0]->name)){
 */                /* dump($seriesApiResponse->name);
                dump(isset($seriesApiResponse->created_by[0]->name));
                dump($seriesApiResponse->overview);
                dump($seriesApiResponse->backdrop_path); */
                /* $r = rand(1,1500);
                break;
            } */

            $handle = curl_init("https://api.themoviedb.org/3/tv/$r?api_key=$apiKey&language=fr-FR");
           
            if($this->checkApiUrlResponse($handle) == 200){

                $seriesApiResponse = (object) json_decode(file_get_contents("https://api.themoviedb.org/3/tv/$r?api_key=$apiKey&language=fr-FR"));
                
                if(!in_array($r, $addedSeriesId) & !empty($seriesApiResponse->overview) & !empty($seriesApiResponse->backdrop_path) & isset($seriesApiResponse->created_by[0]->name)){
                    
                
//-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-Creation Series-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-
                    $series = new Series();
                    $entityManager->persist($series);

                    $series->setTitle($seriesApiResponse->name);

                    $series->setSynopsis(!$seriesApiResponse->overview ? "$lorem" : $seriesApiResponse->overview);

                    $series->setReleaseDate(new DateTimeImmutable($seriesApiResponse->first_air_date));

                    $series->setImage(empty($seriesApiResponse->backdrop_path) ? 'https://i.ibb.co/ySnm17G/Keskonmate.png' : 'https://www.themoviedb.org/t/p/w1920_and_h800_multi_faces'.$seriesApiResponse->backdrop_path);

                    $series->setDirector(!isset($seriesApiResponse->created_by[0]->name) ? 'Xavier Muspimerol' : $seriesApiResponse->created_by[0]->name);

                    $series->setNumberOfSeasons($seriesApiResponse->number_of_seasons);

                    $series->setCreatedAt($currentDateTime);

                    foreach($seriesApiResponse->genres as $key => $value){
                        $genreId = !isset($seriesApiResponse->genres[$key]->id) ? '-none' : $seriesApiResponse->genres[$key]->id;
                        try{
                        $series->addGenre($this->getReference(self::GENRE_ID."$genreId"));
                        }catch(Exception $e){}
                    }
                    
    //-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-Creation Actor-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-
                    $handle = curl_init("https://api.themoviedb.org/3/tv/$r/credits?api_key=$apiKey&language=fr-FR");
                    if($this->checkApiUrlResponse($handle) == 200){
                        $seriessActorList = (object) json_decode(file_get_contents("https://api.themoviedb.org/3/tv/$r/credits?api_key=$apiKey&language=fr-FR"));
                        
                        if(!empty($seriessActorList->cast)){
                            
                            $nbActors = 1;
                            $addedactors = [];
                            foreach ($seriessActorList->cast as $index => $actorsInfo) {
                                /* dd($seriessActorList->cast[0]->id); */
                                $actor = new Actor();
                                $entityManager->persist($actor);
                                
                                $actor->setName($actorsInfo->name);
                                
                                $actor->setImage(empty($actorsInfo->profile_path) ? 'https://www.themoviedb.org/assets/2/v4/glyphicons/basic/glyphicons-basic-4-user-grey-d8fe957375e70239d6abdd549fd7568c89281b2179b5f4470e2e12895792dfa5.svg' : "https://www.themoviedb.org/t/p/w138_and_h175_face$actorsInfo->profile_path");
                                
                                $actor->setCreatedAt($currentDateTime);

                                $this->setReference(self::ACTOR_ID."$actorsInfo->id", $actor);
                    
                                $actorId = $seriessActorList->cast[$index]->id;
                                try{
                                    $series->addActor($this->getReference(self::ACTOR_ID."$actorId"));
                                }catch(Exception $e){}
                                $addedactors[] = $nbActors;
                                if ($nbActors++ == 5) break;
                            }
                            //dump(count($addedactors));
                        }
                    }
    
    //-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-Creation Season-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-_-
                    if(!empty($seriesApiResponse->seasons)){                
                        foreach($seriesApiResponse->seasons as $key => $seasonInfo){
                            
                            $season = new Season();
                            $entityManager->persist($season);
                            
                            $season->setSeasonNumber($seasonInfo->season_number);
                            
                            $season->setNumberOfEpisodes($seasonInfo->episode_count);

                            $season->setImage(empty($seasonInfo->poster_path) ? 'https://www.themoviedb.org/assets/2/v4/glyphicons/basic/glyphicons-basic-38-picture-grey-c2ebdbb057f2a7614185931650f8cee23fa137b93812ccb132b9df511df1cfac.svg' : "https://www.themoviedb.org/t/p/w130_and_h195_bestv2$seasonInfo->poster_path");
                            
                            $season->setCreatedAt($currentDateTime);

                            $this->addReference(self::SEASON_ID."$seasonInfo->id", $season);
                        }

                        foreach($seriesApiResponse->seasons as $key => $seasonInfo){
                            $seasonId = $seriesApiResponse->seasons[$key]->id;
                            try{
                            $series->addSeason($this->getReference(self::SEASON_ID."$seasonId"));
                            }catch(Exception $e){
                                dump('iiiiiiiiiiiiiiiiiiiccccccccccccccccccccccccciiiiiiiiiiiiiiiiiiiiiii');
                                dd($e);
                            }
                        }
                    }

                    $entityManager->flush();
                    array_push($addedSeriesId, $r);
                    dump(count($addedSeriesId));
                }else{
                    dump($seriesApiResponse->name);
                    dump(isset($seriesApiResponse->created_by[0]->name));
                    dump($seriesApiResponse->overview);
                    dump($seriesApiResponse->backdrop_path);
                }
            }//200             
        }//50
    }

    public function checkApiUrlResponse($handle)
    {
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
        curl_exec($handle);
        $statusCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        return $statusCode;
    }
}

