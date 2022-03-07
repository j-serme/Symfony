<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Form\ImpressionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImpressionController extends AbstractController
{
    /**
     * @Route("/impression/new/{id}", name="impression_new")
     */
    public function new(Film $film, EntityManagerInterface $manager, Request $request): Response
    {

        $impression = new Impression();

        $formImpression = $this->createForm(ImpressionType::class, $impression);

        $formImpression->handleRequest($request);

        if ($formImpression->isSubmitted() && $formImpression->isValid())
        {
            $impression->setFilm($film);
            $impression->setUser($this->getUser());
            $impression->setCreatedAt(new \DateTime());
            $manager->persist($impression);
            $manager->flush();
        }



        return $this->redirectToRoute('film', ['id'=>$film->getId()]);
    }

    /**
     * @Route("/impression/delete/{id}", name="impression_delete")
     */
    public function deleteImpression(Impression $impression = null, EntityManagerInterface $manager)
    {

        if ($impression && $this->getUser() == $impression->getUser())
        {
            $manager->remove($impression);
            $manager->flush();
        }

        return $this->redirectToRoute('film', ['id'=>$impression->getFilm()->getId()]);

    }

    /**
     * @Route("/impression/change/{id}",name="impression_change", priority="2")
     * @param Impression $impression
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function change(Impression $impression, Request $request, EntityManagerInterface $manager)
    {
        if ($this->getUser() != $impression->getUser()){

            $this->redirectToRoute('film', ['id'=>$impression->getFilm()->getId()]);
        }

        $formImpression = $this->createForm(ImpressionType::class, $impression);

        $formImpression->handleRequest($request);

        if ($formImpression->isSubmitted() && $formImpression->isValid())
        {


            $manager->persist($impression);
            $manager->flush();

            return $this->redirectToRoute('film', ['id'=>$impression->getFilm()->getId()]);
        }


        return $this->renderForm('impression/edit.html.twig', ['formImpression' => $formImpression, 'impression'=>$impression, 'id' => $impression->getFilm()->getId()]);
    }
}
