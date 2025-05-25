<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document]
class Comment
{
    #[MongoDB\Id]
    private string $id;

    #[MongoDB\ReferenceOne(targetDocument: User::class)]
    private User $userProfile;

    #[MongoDB\ReferenceOne(targetDocument: User::class)]
    private User $author;

    #[MongoDB\Field(type: 'string')]
    #[Assert\NotBlank]
    private string $message;

    #[MongoDB\Field(type: 'date')]
    private \DateTime $commentDate;

    public function __construct()
    {
        $this->commentDate = new \DateTime();
    }

    // Getters y setters...

    public function getId(): string { return $this->id; }

    public function getUserProfile(): User { return $this->userProfile; }
    public function setUserProfile(User $userProfile): void { $this->userProfile = $userProfile; }

    public function getAuthor(): User { return $this->author; }
    public function setAuthor(User $author): void { $this->author = $author; }

    public function getMessage(): string { return $this->message; }
    public function setMessage(string $message): void { $this->message = $message; }

    public function getCommentDate(): \DateTime { return $this->commentDate; }
    public function setCommentDate(\DateTime $commentDate): void { $this->commentDate = $commentDate; }
}
