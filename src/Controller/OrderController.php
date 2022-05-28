<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Dish;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(OrderRepository $orderRepository): Response
    {

        $orders = $orderRepository->findBy(
            ['desk' => '1']
        );

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/order/{id}', name: 'app_ordering')]
    public function order(ManagerRegistry $managerRegistry, Dish $dish)
    {
        $order = new Order();
        $order->setDesk('1');
        $order->setName($dish->getName());
        $order->setOrderNumber($dish->getId());
        $order->setPrice($dish->getPrice());
        $order->setStatus('pending');

        $entityManager = $managerRegistry->getManager();
        $entityManager->persist($order);
        $entityManager->flush();

        $this->addFlash('order', $order->getName() . ' was added to the order');

        return $this->redirect($this->generateUrl('app_menu'));
    }

    #[Route('/status/{id},{status}', name: 'app_status')]
    public function status($id, $status, ManagerRegistry $managerRegistry)
    {
        $entityManager = $managerRegistry->getManager();

        $order = $entityManager->getRepository(Order::class)->find($id);
        $order->setStatus($status);
        $entityManager->flush();

        return $this->redirect($this->generateUrl('app_order'));
    }

    #[Route('/delete/{id}', name: 'order_delete')]
    public function delete($id, OrderRepository $orderRepository, ManagerRegistry $managerRegistry)
    {
        $em = $managerRegistry->getManager();
        $order = $orderRepository->find($id);
        $em->remove($order);
        $em->flush();

        return $this->redirect($this->generateUrl('app_order'));
    }
}
