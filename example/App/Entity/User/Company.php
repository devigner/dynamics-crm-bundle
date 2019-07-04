<?php declare(strict_types=1);

namespace App\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Devigner\DynamicsCRMBundle\Entity\DynamicsEntityTrait;
use Devigner\DynamicsCRMBundle\Entity\DynamicsInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 * @ORM\Table(name="app_company")
 */
class Company implements DynamicsInterface
{
    use DynamicsEntityTrait;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=255, nullable=true)
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="postal_code", type="string", length=255, nullable=true)
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="organisation_type", type="string", length=255, nullable=true)
     */
    private $organisationType;

    /**
     * @var string
     *
     * @ORM\Column(name="chamber_of_commerce_number", type="string", length=255, nullable=true)
     */
    private $chamberOfCommerceNumber;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="leanAndGreenEnabled", type="boolean")
     */
    private $leanAndGreenEnabled;

    /**
     * @var User[]|Collection
     *
     * @ORM\OneToMany(targetEntity="User", mappedBy="company")
     */
    private $users;

    public function __construct()
    {
        $this->enabled = true;
        $this->users = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return User[]|Collection
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @param User[]|Collection $users
     */
    public function setUsers(Collection $users): void
    {
        foreach ($users as $user) {
            $this->addUser($user);
        }
    }

    /**
     * @param User $user
     */
    public function addUser(User $user): void
    {
        $user->setCompany($this);
        if ($this->users->contains($user)) {
            return;
        }

        $this->users->add($user);
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user): void
    {
        if (!$this->users->contains($user)) {
            return;
        }

        $this->users->remove($user);
        $user->setCompany(null);
    }

    /**
     * @return bool
     */
    public function canSync(): bool
    {
        return true;
    }

    public function __toString()
    {
        return $this->name;
    }
}
