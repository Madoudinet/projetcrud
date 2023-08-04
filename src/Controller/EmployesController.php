<?php

namespace App\Controller;

use App\Entity\Employes;
use App\Form\EmployesType;
use App\Repository\EmployesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmployesController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('employes/index.html.twig');
    }

    #[Route('/gestion', name:'gestion')]
    public function gestion(EmployesRepository $repo)
    {
        $employes = $repo->findAll();
        return $this->render('employes/gestion.html.twig', [
            'employes' => $employes,
        ]);
    }

    #[Route('/form/modifier/{id}', name:'modifier')]
    #[Route('/form', name:'form')]
    public function form(Request $globals, EntityManagerInterface $manager, Employes $employe = null): Response
    {
        if($employe == null){
            $employe = new Employes;
        }
        $form = $this->createForm(EmployesType::class, $employe);

        $form->handleRequest($globals);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($employe);
            $manager->flush();
            return $this->redirectToRoute('gestion');
        }

        return $this->render('employes/form.html.twig', [
            'form' => $form,
            'editMode' => $employe->getId() !== null,
        ]);
    }

    #[Route('/employe/{id}', name:'employe')]
    public function show($id, EmployesRepository $repo)
    {
        $employe = $repo->find($id);
        return $this->render('employes/show.html.twig', [
            'employe' => $employe,
        ]);
    }

    #[Route('/employe/supprimer/{id}', name:'supprimer')]
    public function supprimer(Employes $employe, EntityManagerInterface $manager)
    {
        $manager->remove($employe);
        $manager->flush();
        return $this->redirectToRoute('gestion');
    }

    #[Route('/employes', name:'employes')]
    public function employes(EmployesRepository $repo)
    {
        $employes = $repo->findAll();
        return $this->render('employes/employes.html.twig', [
            'employes' => $employes,
        ]);
    }
}
