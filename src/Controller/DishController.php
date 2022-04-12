<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Form\DishType;
use App\Repository\DishRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/gericht', name: 'dish_')]
class DishController extends AbstractController
{
    #[Route('/', name: 'edit')]
    public function index(DishRepository $dishRepository): Response
    {

        $dishes = $dishRepository->findAll();
        return $this->render('dish/index.html.twig', [
            'dishes' => $dishes
        ]);
    }

    #[Route('/anlegen', name: 'create')]
    public function create(ManagerRegistry $managerRegistry, Request $request): Response
    {
        $dish = new Dish();
        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $managerRegistry->getManager();
            $img = $request->files->get('dish')['image'];

            if ($img) {
                $fileName = md5(uniqid()) . '.' . $img->guessClientExtension();
            }
            $img->move(
                $this->getParameter('images_folder'),
                $fileName
            );

            $dish->setImage($fileName);
            $em->persist($dish);
            $em->flush();

            return $this->redirect($this->generateUrl('dish_edit'));
        }


        return $this->render('dish/create.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/entfernen/{id}', name: 'remove')]
    public function delete($id, DishRepository $dishRepository, ManagerRegistry $managerRegistry)
    {
        $em = $managerRegistry->getManager();
        $dish = $dishRepository->find($id);
        $em->remove($dish);
        $em->flush();

        $this->addFlash('success', 'Gericht wurde erfolgreich entfernt');

        return $this->redirect($this->generateUrl('dish_edit'));
    }

    #[Route('/anzeigen/{id}', name: 'show')]
    public function show(Dish $dish)
    {
        return $this->render('dish/show.html.twig', [
            'dish' => $dish
        ]);
    }
}
