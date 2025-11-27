<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\WeekDays;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::JSON)]
    private array $schedule = [];

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    private ?string $country = null;

    /** @var Collection<int, ServiceProvider> */
    #[ORM\ManyToMany(targetEntity: ServiceProvider::class, inversedBy: 'clients')]
    #[ORM\JoinTable(name: 'client_service_provider')]
    private Collection $serviceProviders;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'client')]
    private Collection $orders;

    public function __construct()
    {
        $this->serviceProviders = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

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

    /** @return WeekDays[] */
    public function getSchedule(): array
    {
        return array_map(WeekDays::from(...), $this->schedule);
    }

    public function setSchedule(array $schedule): self
    {
        $normalized = array_map(static function ($day): WeekDays {
            if ($day instanceof WeekDays) {
                return $day;
            }

            if (\is_string($day)) {
                return WeekDays::from($day);
            }

            throw new \InvalidArgumentException('Schedule values must be WeekDays instances or enum names.');
        }, $schedule);

        $this->schedule = array_map(static fn(WeekDays $day) => $day->value, $normalized);

        return $this;
    }

    public function addWeekDayToSchedule(WeekDays $day): self
    {
        if (!\in_array($day->value, $this->schedule, true)) {
            $this->schedule[] = $day->value;
        }

        return $this;
    }

    public function getFullAddress(): string
    {
        return "{$this->address}, {$this->city}, {$this->country}";
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection<int, ServiceProvider>
     */
    public function getServiceProviders(): Collection
    {
        return $this->serviceProviders;
    }

    public function addServiceProvider(ServiceProvider $serviceProvider): self
    {
        if (!$this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->add($serviceProvider);
        }

        return $this;
    }

    public function removeServiceProvider(ServiceProvider $serviceProvider): self
    {
        if ($this->serviceProviders->contains($serviceProvider)) {
            $this->serviceProviders->removeElement($serviceProvider);
        }

        return $this;
    }

    public function setServiceProviders(array $serviceProviders): self
    {
        $this->serviceProviders = new ArrayCollection($serviceProviders);

        return $this;
    }

    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
        }

        return $this;
    }

    public function setOrders(array $orders): self
    {
        $this->orders = new ArrayCollection($orders);

        return $this;
    }
}
