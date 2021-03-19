<?php

namespace App\Entity;

use App\Repository\ShippingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ShippingRepository::class)
 */
class Shipping
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Please fill the city")
     * @Assert\NotNull(message="This field is mandaory")
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $city;

    /**
     * @Assert\NotBlank(message="Please fill the Zip code")
     * @Assert\NotNull(message="This field is mandatory")
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $postalCode;

    /**
     * @Assert\NotBlank(message="Please set the address")
     * @Assert\NotNull(message="This field is mandatory")
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $address;

    /**
     * @Assert\NotBlank(message="Please fill you first name")
     * @Assert\NotNull(message="This field is mandarory")
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $firstName;

    /**
     * @Assert\NotBlank(message="Please fill you last name")
     * @Assert\NotNull(message="This field is mandarory")
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $Email;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $mobileNumber;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $state;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    /**
     *
     * @param string $Email
     * @return self
     */
    public function setEmail(string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

    /**
     *
     * @return string|null
     */
    public function getMobileNumber(): ?string
    {
        return $this->mobileNumber;
    }

    /**
     *
     * @param string $mobileNumber
     * @return self
     */
    public function setMobileNumber(string $mobileNumber): self
    {
        $this->mobileNumber = $mobileNumber;

        return $this;
    }

    /**
     *
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     *
     * @param string $country
     * @return self
     */
    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     *
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     *
     * @param string|null $state
     * @return self
     */
    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }
}
