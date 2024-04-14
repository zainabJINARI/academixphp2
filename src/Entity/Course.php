<?php

namespace App\Entity;

use App\Enum\CourseCategories;
use App\Repository\CourseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column]
    private ?float $nbrHours = null;

    #[ORM\Column(length: 255)]
    private ?string $level = null;

    #[ORM\Column(length: 255)]
    private ?string $thumbnail = null;

    #[ORM\Column(length: 255)]
    private ?int $nbrLessons = null;

    #[ORM\ManyToOne(inversedBy: 'courses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $tutor = null;

    
    #[ORM\Column(length:255)]    
    private $category= CourseCategories::TECHINDUSTRY;

    public function getcategory(): ?string
    {
        return $this->category;
    }
    public function setcategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getNbrLessons(): ?int
    {
        return $this->nbrLessons;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getNbrHours(): ?float
    {
        return $this->nbrHours;
    }

    public function setNbrHours(float $nbrHours): static
    {
        $this->nbrHours = $nbrHours;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): static
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getTutor(): ?User
    {
        return $this->tutor;
    }

    public function setTutor(?User $tutor): static
    {
        $this->tutor = $tutor;

        return $this;
    }
}
