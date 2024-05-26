<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column]
    private ?int $idCourse = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?int $nbrLessons = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbrHours = null;


    #[ORM\Column(name: "module_order")]
    private ?int $order = null;

    public function getIdCourse(): ?int
    {
        return $this->idCourse;
    }

    public function setIdCourse(?int $idCourse): static
    {
        $this->idCourse = $idCourse;

        return $this;
    }
    
    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(?int $order): static
    {
        $this->order = $order;
        return $this;
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

    public function getNbrLessons(): ?int
    {
        return $this->nbrLessons;
    }

    public function setNbrLessons(?int $nbrLessons): static
    {
        $this->nbrLessons = $nbrLessons;

        return $this;
    }

    public function getNbrHours(): ?int
    {
        return $this->nbrHours;
    }

    public function setNbrHours(?int $nbrHours): static
    {
        $this->nbrHours= $nbrHours;

        return $this;
    }

   
}
