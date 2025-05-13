<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document]
class UserContent
{
    #[MongoDB\Id]
    private string $id;

    #[MongoDB\ReferenceOne(targetDocument: User::class)]
    private User $user;

    #[MongoDB\ReferenceOne(targetDocument: Content::class)]
    private Content $content;

    #[MongoDB\Field(type: 'date')]
    private \DateTime $addedAt;

    #[MongoDB\Field(type: 'string')]
    #[Assert\Choice(choices: ['pendiente', 'viendo', 'vista'])]
    private string $status = 'pendiente';

    #[MongoDB\Field(type: 'float', nullable: true)]
    private ?float $rating = null;

    // Getters y setters

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function setContent(Content $content): void
    {
        $this->content = $content;
    }

    public function getAddedAt(): \DateTime
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTime $addedAt): void
    {
        $this->addedAt = $addedAt;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): void
    {
        $this->rating = $rating;
    }
}
