<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\NoteRepository;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity( 
    fields :['user','recette'],
    errorPath: 'user',
    message: "Cet utilisateur a déjà mis une noté à cette recette."    
)]
#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $note = null;

    #[ORM\ManyToOne(inversedBy: 'recette')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recette $recette = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable() ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRecette(): ?Recette
    {
        return $this->recette;
    }

    public function setRecette(?Recette $recette): self
    {
        $this->recette = $recette;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
