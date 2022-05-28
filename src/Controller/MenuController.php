<?php

namespace App\Controller;

use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu')]
    public function menu(DishRepository $dishRepository): Response
    {

        $dishes = $dishRepository->findAll();

        return $this->render('menu/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }
}
