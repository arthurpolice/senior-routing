<?php

namespace App\Entity;

use App\Repository\ServiceProviderRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Enum\WeekDays;

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

    public function getFullAddress(): string
    {
        return "{$this->address}, {$this->city}, {$this->country}";
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function getClients(): Collection
    {
        return $this->clients;
    }
}
