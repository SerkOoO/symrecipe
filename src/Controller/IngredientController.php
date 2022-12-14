<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    /**
     * La fonction permet de voir tout les ingrédients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient_index', methods:['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        //Pagination
        $ingredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
         
        );
      
        return $this->render('pages/ingredient/index.html.twig', [
        'ingredients' => $ingredients
        ]);
    }
//-----------------------------------------------------------------------------------------------------------------------------------
    /**
     * Ce controller permet de montrer un formulaire pour créer un ingrédient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/ingredient/nouveau",'ingredient.new',methods: ['GET','POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager) : Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        
        //Validation d'ajout d'un ingrédient "Persistance"
        if ($form->isSubmitted() && $form->isValid()) { 
            $ingredient=$form->getData();
           $manager->persist($ingredient);
           $manager->flush();

          return $this->redirectToRoute('ingredient_index');
           $this->addFlash(
               'success',
               "L'ingrédient a été crée avec succés"
           );

        }
        return $this->render('pages/ingredient/new.html.twig', [
            'form' =>$form->createView()
        ]);
    }
//-----------------------------------------------------------------------------------------------------------------------------------

/**
 * Ce controller permet d'edit un ingrédient
 */
    #[Route('/ingredient/edition/{id}','ingredient.edit',methods:['GET', 'POST'])]
    public function edit(Ingredient $ingredient,Request $request, EntityManagerInterface $manager) : Response{

        
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        
        //Modifie les valeurs de l'ingrédient
        if ($form->isSubmitted() && $form->isValid()) { 
            $ingredient=$form->getData();
           $manager->persist($ingredient);
           $manager->flush();

          
           $this->addFlash(
               'success',
               "L'ingrédient a été modifié avec succés"
           );
           return $this->redirectToRoute('ingredient_index');
        }

        return $this->render('pages/ingredient/edit.html.twig',[
            'form'=>$form->createView()
        ]);
    }
//-----------------------------------------------------------------------------------------------------------------------------------
/**
 * Ce controller permet de supprimer un ingrédient
 */
#[Route('/ingredient/suppression/{id}','ingredient.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient) : Response
    {


        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'ingrédient a été supprimé avec succés"
        );

        return $this->redirectToRoute('ingredient_index');
    }

    
}
