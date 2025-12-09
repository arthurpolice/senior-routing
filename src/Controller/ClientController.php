<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\NewClientFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Order;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'app_client')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $clients = $entityManager->getRepository(Client::class)->findAll();
        return $this->render('client/index.html.twig', ['clients' => $clients]);
    }

    #[Route('/client/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $form = $this->createForm(NewClientFormType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();
            $this->addFlash('success', 'Client created successfully.');

            return $this->redirectToRoute('app_client_show', ['id' => $client->getId()]);
        }

        return $this->render('client/new_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/client/{id}', name: 'app_client_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $client = $entityManager->find(Client::class, $id);
        if (null === $client) {
            throw $this->createNotFoundException(\sprintf('Client with id %d was not found.', $id));
        }

        return $this->render('client/show.html.twig', ['client' => $client]);
    }

    #[Route('/client/{id}/orders', name: 'app_client_orders', methods: ['GET'])]
    public function orders(int $id, EntityManagerInterface $entityManager): Response
    {
        $client = $entityManager->find(Client::class, $id);
        if (null === $client) {
            throw $this->createNotFoundException(\sprintf('Client with id %d was not found.', $id));
        }
        return $this->render('client/orders.html.twig', ['client' => $client]);
    }

    #[Route('/client/{id}/orders/new', name: 'app_client_orders_new', methods: ['GET', 'POST'])]
    public function newOrder(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = $entityManager->find(Client::class, $id);
        if (null === $client) {
            throw $this->createNotFoundException(\sprintf('Client with id %d was not found.', $id));
        }
        
        return $this->render('client/new_order_form.html.twig', ['client' => $client]);
    }

    #[Route('/client/{id}/orders/{orderId}/remove', name: 'app_client_orders_remove', methods: ['POST'])]
    public function removeOrder(int $id, int $orderId, EntityManagerInterface $entityManager): Response
    {
        $client = $entityManager->find(Client::class, $id);
        if (null === $client) {
            throw $this->createNotFoundException(\sprintf('Client with id %d was not found.', $id));
        }
        $order = $entityManager->find(Order::class, $orderId);
        if (null === $order) {
            throw $this->createNotFoundException(\sprintf('Order with id %d was not found.', $orderId));
        }
        $entityManager->remove($order);
        $entityManager->flush();
        return $this->redirectToRoute('app_client_orders', ['id' => $id]);
    }
}