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

    #[ORM\Column(nullable: true)]
    private ?float $nbrHours = null;

    #[ORM\Column(length: 255)]
    private ?string $level = null;

    #[ORM\Column(length: 255)]
    private ?string $thumbnail = 'https://training.digitalscholar.in/images/default-course-thumbnail.png';

    #[ORM\Column]
    private ?int $nbrLessons = 0;

    #[ORM\ManyToOne(inversedBy: 'courses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $tutor = null;

    #[ORM\Column(length: 255)]
    private ?string $category = CourseCategories::TECHINDUSTRY;

    #[ORM\Column]
    private bool $active = false;

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
        return $this->nbrHours ?: 0;
    }

    public function setNbrHours(float $nbrHours): static
    {
        $this->nbrHours = $nbrHours;
        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level ?: 'Beginner';
    }

    public function setLevel(string $level): static
    {
        $this->level = $level ?: 'Beginner';
        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail ?: 'https://training.digitalscholar.in/images/default-course-thumbnail.png';
    }

    public function setThumbnail(?string $thumbnail): static
    {
        $this->thumbnail = $thumbnail ?: 'https://training.digitalscholar.in/images/default-course-thumbnail.png';
        return $this;
    }

    public function getNbrLessons(): ?int
    {
        return $this->nbrLessons ?: 0;
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

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;
        return $this;
    }
}
