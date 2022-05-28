<?php

namespace App\Controller;

use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(DishRepository $dishRepository): Response
    {

        $dishes = $dishRepository->findAll();
        $random = array_rand($dishes, 2);

        return $this->render('home/index.html.twig', [
            'firstDish' => $dishes[$random[0]],
            'secondDish' => $dishes[$random[1]],
        ]);
    }
}
