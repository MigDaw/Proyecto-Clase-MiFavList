<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ODM\Document(collection: "users")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[MongoDB\Id]
    private string $id;

    #[MongoDB\Field(type: 'string')]
    #[Assert\NotBlank]
    private string $username;

    #[MongoDB\Field(type: 'string')]
    #[Assert\Email]
    private string $email;

    #[MongoDB\Field(type: 'string')]
    private string $password;

    #[MongoDB\Field(type: 'string', nullable: true)]
    private ?string $profilePic = null;

    #[MongoDB\Field(type: 'bool')]
    private bool $perfilPublic = true;

    #[MongoDB\Field(type: 'bool')]
    private bool $chatPublic = true;

    // Métodos requeridos por UserInterface
    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getSalt(): ?string
    {
        return null; // No se necesita con bcrypt o sodium
    }

    public function eraseCredentials(): void
    {
        // Aquí podrías limpiar datos sensibles temporales
    }

    // Getters y Setters
    public function getId(): ?string { return $this->id; }

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): self {
        $this->username = $username; return $this;
    }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self {
        $this->email = $email; return $this;
    }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self {
        $this->password = $password; return $this;
    }

    public function getProfilePic(): ?string { return $this->profilePic; }
    public function setProfilePic(?string $profilePic): self {
        $this->profilePic = $profilePic; return $this;
    }

    public function isPerfilPublic(): bool { return $this->perfilPublic; }
    public function setPerfilPublic(bool $perfilPublic): self {
        $this->perfilPublic = $perfilPublic; return $this;
    }

    public function isChatPublic(): bool { return $this->chatPublic; }
    public function setChatPublic(bool $chatPublic): self {
        $this->chatPublic = $chatPublic; return $this;
    }
}
