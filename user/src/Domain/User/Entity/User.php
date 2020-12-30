<?php

declare(strict_types=1);

namespace App\Domain\User\Entity;

use App\Domain\User\Enum\RoleEnum;
use App\Domain\User\ValueObject\PersonalData;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\User\Repository\Doctrine\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="guid", unique=true)
     */
    private $id;

    /**
     * @var PersonalData
     *
     * @ORM\Embedded(class=PersonalData::class, columnPrefix=false)
     */
    private $personalData;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $roles;

    private function __construct(string $id, string $email, PersonalData $personalData, array $roles)
    {
        $this->id = $id;
        $this->email = $email;
        $this->personalData = $personalData;
        $this->roles = $roles;
    }

    public static function createUser(string $id, string $email, PersonalData $personalData): self
    {
        return new self($id, $email, $personalData, [RoleEnum::ROLE_USER]);
    }

    public static function createAdmin(string $id, string $email, PersonalData $personalData): self
    {
        return new self($id, $email, $personalData, [RoleEnum::ROLE_USER, RoleEnum::ROLE_ADMIN]);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getSalt(): string
    {
        return '';
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void {}

    public function updateData(string $email, string $firstName, string $lastName): void
    {
        $this->email = $email;
        $this->personalData = new PersonalData($firstName, $lastName);
    }
}
