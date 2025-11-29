<?php

namespace App\Controller\Api;

use App\Entity\Order;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class OrderController extends AbstractController
{
    public function __construct(
        private OrderService $orderService,
    ) {
    }

    #[Route('/orders/batch', name: 'api_orders_batch_create', methods: ['POST'])]
    public function batchCreate(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['orders']) || !is_array($data['orders'])) {
            return $this->json([
                'success' => false,
                'message' => 'Invalid request format. Expected "orders" array.'
            ], Response::HTTP_BAD_REQUEST);
        }

        if (empty($data['orders'])) {
            return $this->json([
                'success' => false,
                'message' => 'No orders provided.'
            ], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->orderService->createBatch($data['orders']);

        if (!empty($result['errors']) && empty($result['created'])) {
            return $this->json([
                'success' => false,
                'message' => 'All orders failed validation.',
                'errors' => $result['errors']
            ], Response::HTTP_BAD_REQUEST);
        }

        $response = [
            'success' => true,
            'message' => sprintf('%d order(s) created successfully.', count($result['created'])),
            'createdCount' => count($result['created']),
            'orderIds' => array_map(fn(Order $o) => $o->getId(), $result['created']),
        ];

        if (!empty($result['errors'])) {
            $response['partialErrors'] = $result['errors'];
        }

        return $this->json($response, Response::HTTP_CREATED);
    }
}
