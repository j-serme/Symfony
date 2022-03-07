<?php

namespace App\Controller;

use App\Entity\Film;
use App\Entity\Impression;
use App\Form\FilmType;
use App\Form\ImpressionType;
use App\Repository\FilmRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilmController extends AbstractController
{
    /**
     * @Route("/", name="films")
     */
    public function index(FilmRepository $repo): Response
    {
        return $this->render('film/index.html.twig', [
            'films' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/film/{id}", name="film")
     */
    public function show(Film $film)
    {
        $impression = new Impression();

        $formImpression = $this->createForm(ImpressionType::class, $impression);

        return $this->renderForm('film/show.html.twig', ['film'=>$film, 'formImpression'=>$formImpression]);

    }

    /**
     * @Route("/film/add", name="film_add", priority="1")
     */
    public function new(Request $request, EntityManagerInterface $manager)
    {


        $film = new Film();

        $form = $this->createForm(FilmType::class, $film);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $film->setUser($this->getUser());
            $manager->persist($film);
            $manager->flush();

            return $this->redirectToRoute('films');
        }

        return $this->renderForm('film/new.html.twig', ['form'=>$form]);

    }

    /**
     * @Route("/film/delete/{id}", name="film_delete")
     */
    public function delete(Film $film = null, EntityManagerInterface $manager)
    {

        if ($film && $this->getUser() == $film->getUser())
        {
            $manager->remove($film);
            $manager->flush();
        }

        return $this->redirectToRoute('films');

    }


    /**
     * @Route("/film/modify/{id}", name="film_modify")
     * @param Film $film
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function modify(Film $film, EntityManagerInterface $manager, Request $request)
    {

        if (!$this->getUser() == $film->getUser()) {

            $this->redirectToRoute('films');
        }


        $form = $this->createForm(FilmType::class , $film);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {


            $manager->persist($film);
            $manager->flush();

            return $this->redirectToRoute('films');

        }

        return $this->renderForm('film/modify.html.twig', ['form'=>$form]);
    }
}
