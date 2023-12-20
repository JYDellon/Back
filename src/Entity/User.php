<?php

namespace App\Entity;

use App\Entity\Traits\CreatedAtTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface

{
    use CreatedAtTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $firstName;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 2, max: 50)]
    private ?string $lastName;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Email()]
    private ?string $email;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 8)]
    private ?string $password;

    #[ORM\Column(type: 'json')]
    #[Assert\NotNull()]
    private array $roles = [];

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $accountStatus;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(type: "boolean")]
    private $isEmailConfirmed;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $confirmationToken;


    //Relations
    #[ORM\OneToOne(mappedBy: 'cartUser')]
    private ?Cart $cartUser;

    #[ORM\OneToMany(mappedBy: 'orderUser', targetEntity: Order::class)]
    private Collection $orderUser;

    #[ORM\OneToMany(mappedBy: 'reviewUser', targetEntity: Review::class)]
    private Collection $reviewUser;

    #[ORM\OneToMany(mappedBy: 'userAddress', targetEntity: Address::class)]
    private Collection $userAddress;

    public function __construct()
    {
        // Initialise createdAt à la date et heure actuelles.
        $this->createdAt = new \DateTime(); // Utilisation de DateTime        // Initialisation des autres propriétés.
        $this->orderUser = new ArrayCollection();
        $this->reviewUser = new ArrayCollection();
        $this->userAddress = new ArrayCollection();
        $this->isEmailConfirmed = false;
        $this->confirmationToken = bin2hex(random_bytes(32));
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // garantir que chaque utilisateur a au moins ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getAccountStatus(): ?string
    {
        return $this->accountStatus;
    }

    public function setAccountStatus(string $accountStatus): static
    {
        $this->accountStatus = $accountStatus;

        return $this;
    }

    /**
     * Get the value of resetToken
     */
    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    /**
     * Set the value of resetToken
     */
    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }


    public function setIsEmailConfirmed(bool $isEmailConfirmed): self
    {
        $this->isEmailConfirmed = $isEmailConfirmed;

        return $this;
    }

    public function getIsEmailConfirmed(): bool
    {
        return $this->isEmailConfirmed;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function  getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function eraseCredentials()
    {
        // Remove sensitive data from the user
        // $this->plainPassword = null;
    }

    public function getCartUser(): ?Cart
    {
        return $this->cartUser;
    }

    public function setCartUser(Cart $cartUser): static
    {
        // set the owning side of the relation if necessary
        if ($cartUser->getCartUser() !== $this) {
            $cartUser->setCartUser($this);
        }

        $this->cartUser = $cartUser;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrderUser(): Collection
    {
        return $this->orderUser;
    }

    public function addOrderUser(Order $orderUser): static
    {
        if (!$this->orderUser->contains($orderUser)) {
            $this->orderUser->add($orderUser);
            $orderUser->setOrderUser($this);
        }

        return $this;
    }

    public function removeOrderUser(Order $orderUser): static
    {
        if ($this->orderUser->removeElement($orderUser) && $orderUser->getOrderUser() === $this) {
            // set the owning side to null (unless already changed)
            $orderUser->setOrderUser(null);
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviewUser(): Collection
    {
        return $this->reviewUser;
    }

    public function addReviewUser(Review $reviewUser): static
    {
        if (!$this->reviewUser->contains($reviewUser)) {
            $this->reviewUser->add($reviewUser);
            $reviewUser->setReviewUser($this);
        }

        return $this;
    }

    public function removeReviewUser(Review $reviewUser): static
    {
        if ($this->reviewUser->removeElement($reviewUser) && $reviewUser->getReviewUser() === $this) {
            // set the owning side to null (unless already changed)
            $reviewUser->setReviewUser(null);
        }

        return $this;
    }


    /**
     * @return Collection<int, Address>
     */
    public function getUserAddress(): Collection
    {
        return $this->userAddress;
    }

    public function addUserAddress(Address $userAddress): static
    {
        if (!$this->userAddress->contains($userAddress)) {
            $this->userAddress->add($userAddress);
            $userAddress->setUserAddress($this);
        }

        return $this;
    }

    public function removeUserAddress(Address $userAddress): static
    {
        if ($this->userAddress->removeElement($userAddress) && $userAddress->getUserAddress() === $this) {
            // set the owning side to null (unless already changed)
            $userAddress->setUserAddress(null);
        }

        return $this;
    }


    /**
     * Get the value of password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }
}
