<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Entity\Like;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LikeController extends AbstractController
{
    /**
     * @Route("/film/like/{id}", name="like_film", priority="1")
     */
    public function likeFilm(Film $film, EntityManagerInterface $manager,LikeRepository $repo): Response
    {

        $like= $repo->findOneBy(['film'=>$film,
            'user'=>$this->getUser()]);

        if(!$like){


            $like = new Like();
            $like->setUser($this->getUser());
            $like->setFilm($film);

            $manager->persist($like);




        } else {

            $manager->remove($like);

        }


        $manager->flush();


        return $this->redirectToRoute('film', ['id'=>$like->getFilm()->getId()]);

    }

    /**
     * @Route("/film/impression/like/{id}", name="like_impression", priority="1")
     */
    public function likeImpression(Impression $impression, EntityManagerInterface $manager,LikeRepository $repo): Response
    {

        $like= $repo->findOneBy(['impression'=>$impression,
            'user'=>$this->getUser()]);

        if(!$like){


            $like = new Like();
            $like->setUser($this->getUser());
            $like->setImpression($impression);

            $manager->persist($like);




        } else {

            $manager->remove($like);

        }


        $manager->flush();


        return $this->redirectToRoute('film', ['id'=>$impression->getFilm()->getId()]);

    }


}
