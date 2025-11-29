<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ServiceProvider;
use App\Form\NewServiceProviderFormType;
use App\Service\Routes\RoutePlanner;
use App\Dto\RouteRequestDto;

class ServiceProviderController extends AbstractController
{
    #[Route('/service-provider', name: 'app_service_provider')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $serviceProviders = $entityManager->getRepository(ServiceProvider::class)->findAll();
        return $this->render('service_provider/index.html.twig', [
            'serviceProviders' => $serviceProviders,
        ]);
    }

    #[Route('/service-provider/new', name: 'app_service_provider_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $serviceProvider = new ServiceProvider();
        $form = $this->createForm(NewServiceProviderFormType::class, $serviceProvider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($serviceProvider);
            $entityManager->flush();
            $this->addFlash('success', 'Service provider created successfully.');

            return $this->redirectToRoute('app_service_provider');
        }

        return $this->render('service_provider/new_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/service-provider/route', name: 'app_service_provider_route', methods: ['POST'])]
    public function route(
        #[MapRequestPayload] RouteRequestDto $routeRequestDto,
        RoutePlanner $routePlanner,
        EntityManagerInterface $entityManager,
    ): Response {
        $serviceProvider = $entityManager->find(ServiceProvider::class, $routeRequestDto->serviceProviderId);
        if (null === $serviceProvider) {
            throw $this->createNotFoundException(\sprintf('ServiceProvider with id %d was not found.', $routeRequestDto->serviceProviderId));
        }
        return $this->json(['route' => $routePlanner->planRouteForDate($serviceProvider, $routeRequestDto->date ?? new \DateTimeImmutable())]);
    }

    #[Route('/service-provider/{id}/', name: 'app_service_provider_show', methods: ['GET'])]
    public function show(int $id, EntityManagerInterface $entityManager): Response {
        $serviceProvider = $entityManager->find(ServiceProvider::class, $id);
        if (null === $serviceProvider) {
            throw $this->createNotFoundException(\sprintf('ServiceProvider with id %d was not found.', $id));
        }
        return $this->render('service_provider/show.html.twig', ['serviceProvider' => $serviceProvider]);
    }
}