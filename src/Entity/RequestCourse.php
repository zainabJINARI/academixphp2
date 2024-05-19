<?php

namespace App\Entity;

use App\Repository\RequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RequestRepository::class)]
class RequestCourse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column]
    private ?int $courseid = null;

    #[ORM\Column]
    private ?string $status = 'pending';

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $time = null;

    #[ORM\Column(length: 400)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $idtutor = null;

    #[ORM\Column]
    private ?string $type = null;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourseId(): ?int
    {
        return $this->courseid;
    }

    public function setCourseId(int $courseid): static
    {
        $this->courseid = $courseid;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }


    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getIdtutor(): ?int
    {
        return $this->idtutor;
    }

    public function setIdtutor(int $idtutor): static
    {
        $this->idtutor = $idtutor;

        return $this;
    }
}
