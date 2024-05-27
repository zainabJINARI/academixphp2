<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class CourseProgress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Course::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Course $course = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $student = null;

    #[ORM\OneToMany(mappedBy: 'courseProgress', targetEntity: ModuleProgress::class, cascade: ['persist', 'remove'])]
    private Collection $moduleProgresses;

    #[ORM\Column]
    private int $totalModules = 0;

    #[ORM\Column]
    private int $completedModules = 0;

    #[ORM\Column]
    private bool $completed = false;

    public function __construct()
    {
        $this->moduleProgresses = new ArrayCollection();
    }
    public function getModuleProgresses(): Collection
    {
        return $this->moduleProgresses;
    }
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(Course $course): static
    {
        $this->course = $course;
        return $this;
    }

    public function getStudent(): ?User
    {
        return $this->student;
    }

    public function setStudent(User $student): static
    {
        $this->student = $student;
        return $this;
    }

    public function getCompletedModules(): ?int
    {
        return $this->completedModules;
    }

    public function setCompletedModules(int $completedModules): static
    {
        $this->completedModules = $completedModules;
        return $this;
    }

    public function getTotalModules(): ?int
    {
        return $this->totalModules;
    }

    public function setTotalModules(int $totalModules): static
    {
        $this->totalModules = $totalModules;
        return $this;
    }

    public function isCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): static
    {
        $this->completed = $completed;
        return $this;
    }
}
