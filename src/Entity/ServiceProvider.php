<?php

namespace App\Entity;

use App\Repository\ServiceProviderRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: ServiceProviderRepository::class)]
class ServiceProvider
{
    public function __construct()
    {
        $this->clients = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $country = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\ManyToMany(targetEntity: Client::class, mappedBy: 'serviceProviders')]
    private Collection $clients;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'serviceProvider')]
    private Collection $orders;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getRouteAddresses(?\DateTimeInterface $date): array
    {
        $orders = $this->orders->toArray();
        $todaysDeliveries = array_filter($orders, static function(Order $order) use ($date): bool {
            $orderDate = $order->getDate();
            $dayDiff = $orderDate->diff($date)->days;
            $isWithinWindow = $dayDiff <= 2 && $orderDate >= $date;
            $isClientAvailable = \in_array($date->format('l'), $order->getClient()->getSchedule(), true);
            return $isWithinWindow && $isClientAvailable;
        });
        return array_map(static fn(Order $order): string => $order->getClient()->getAddress(), $todaysDeliveries);
    }

    public function getClients(): Collection
    {
        return $this->clients;
    }
}
