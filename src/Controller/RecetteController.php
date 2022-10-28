<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecetteController extends AbstractController
{

    /**
     * This controller display all recipes
     *
     * @param RecetteRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recette', name: 'recette.index', methods: ['GET'])]
    public function index(RecetteRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {

        $recettes = $paginator->paginate(
            $repository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recette/index.html.twig', [
            'recettes' => $recettes,
        ]);
    }

    /**
     * This controller allow us tp create a new recipe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/creation', 'recette.new', methods: ['GET', 'POST'])]
    public function new (Request $request, EntityManagerInterface $manager) : Response
    {
        $recette = new Recette();
        $form = $this->createForm(RecetteType::class, $recette);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $recette = $form->getData();

            $manager->persist($recette);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été créée avec succès !'
            );

            return $this->redirectToRoute('recette.index');
        }

        return $this->render('pages/recette/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * This controller allows us to edit a recipe
     *
     * @param Recette $recette
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/edition/{id}', 'recette.edit', methods: ['GET', 'POST'])]
    public function edit(
        Recette $recette,
        Request $request,
        EntityManagerInterface $manager
    ) : Response {
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $recette = $form->getData();

            $manager->persist($recette);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre recette a été modifiée avec succès !'
            );

            return $this->redirectToRoute('recette.index');
        }

        return $this->render('pages/recette/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * This controller allows us to delete a recipe
     *
     * @param EntityManagerInterface $manager
     * @param Recette $recette
     * @return Response
     */
    #[Route('/recette/suppression/{id}', 'recette.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Recette $recette) : Response
    {
        $manager->remove($recette);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre recette a été supprimée avec succès !'
        );

        return $this->redirectToRoute('recette.index');
    }
}
