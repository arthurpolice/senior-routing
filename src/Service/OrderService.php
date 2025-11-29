<?php

namespace App\Service;

use App\Dto\OrderCreationDto;
use App\Entity\Client;
use App\Entity\Order;
use App\Entity\ServiceProvider;
use App\Enum\MealTypes;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @param array<int, array{orderSize: int, date: string, clientId: int, serviceProviderId: int, mealType: string}> $ordersData
     * @return array{created: Order[], errors: array<int, array{index: int, errors: string[]}>}
     */
    public function createBatch(array $ordersData): array
    {
        $createdOrders = [];
        $errors = [];

        foreach ($ordersData as $index => $orderData) {
            $result = $this->createSingleOrder($orderData, $index);
            
            if ($result instanceof Order) {
                $createdOrders[] = $result;
            } else {
                $errors[] = $result;
            }
        }

        if (!empty($createdOrders)) {
            $this->entityManager->flush();
        }

        return ['created' => $createdOrders, 'errors' => $errors];
    }

    /**
     * @return Order|array{index: int, errors: string[]}
     */
    private function createSingleOrder(array $orderData, int $index): Order|array
    {
        try {
            $dto = $this->buildDto($orderData);
            
            $validationErrors = $this->validateDto($dto);
            if (!empty($validationErrors)) {
                return ['index' => $index, 'errors' => $validationErrors];
            }

            $client = $this->entityManager->find(Client::class, $dto->clientId);
            if (!$client) {
                return ['index' => $index, 'errors' => ["Client with ID {$dto->clientId} not found."]];
            }

            $serviceProvider = $this->entityManager->find(ServiceProvider::class, $dto->serviceProviderId);
            if (!$serviceProvider) {
                return ['index' => $index, 'errors' => ["Service provider with ID {$dto->serviceProviderId} not found."]];
            }

            if (!$client->getServiceProviders()->contains($serviceProvider)) {
                return ['index' => $index, 'errors' => ['Service provider is not linked to this client.']];
            }

            $order = $this->buildOrder($dto, $client, $serviceProvider);
            $this->entityManager->persist($order);

            return $order;
        } catch (\Exception $e) {
            return ['index' => $index, 'errors' => [$e->getMessage()]];
        }
    }

    private function buildDto(array $orderData): OrderCreationDto
    {
        $date = isset($orderData['date']) 
            ? new \DateTime($orderData['date']) 
            : new \DateTime();

        return new OrderCreationDto(
            orderSize: $orderData['orderSize'] ?? 0,
            date: $date,
            clientId: $orderData['clientId'] ?? 0,
            serviceProviderId: $orderData['serviceProviderId'] ?? 0,
            mealType: $orderData['mealType'] ?? '',
        );
    }

    /**
     * @return string[]
     */
    private function validateDto(OrderCreationDto $dto): array
    {
        $violations = $this->validator->validate($dto);
        
        if (count($violations) === 0) {
            return [];
        }

        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = $violation->getPropertyPath() . ': ' . $violation->getMessage();
        }

        return $errors;
    }

    private function buildOrder(OrderCreationDto $dto, Client $client, ServiceProvider $serviceProvider): Order
    {
        $order = new Order();
        $order->setClient($client);
        $order->setServiceProvider($serviceProvider);
        $order->setDate($dto->date);
        $order->setOrderSize($dto->orderSize);
        $order->setMealType(MealTypes::from($dto->mealType));

        return $order;
    }
}

