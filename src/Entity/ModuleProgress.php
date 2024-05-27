<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ModuleProgress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Module::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Module $module = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $student = null;

    #[ORM\ManyToOne(targetEntity: CourseProgress::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?CourseProgress $courseProgress = null;

    #[ORM\Column]
    private ?int $completedLessons = 0;

    #[ORM\Column]
    private ?int $totalLessons = 0;

    #[ORM\Column]
    private ?bool $completed = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function getCourseProgress(): ?CourseProgress
    {
        return $this->courseProgress;
    }

    public function setCourseProgress(CourseProgress $courseProgress): static
    {
        $this->courseProgress = $courseProgress;
        return $this;
    }
    
    public function setModule(Module $module): static
    {
        $this->module = $module;
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

    public function getCompletedLessons(): ?int
    {
        return $this->completedLessons;
    }

    public function setCompletedLessons(int $completedLessons): static
    {
        $this->completedLessons = $completedLessons;
        return $this;
    }

    public function getTotalLessons(): ?int
    {
        return $this->totalLessons;
    }

    public function setTotalLessons(int $totalLessons): static
    {
        $this->totalLessons = $totalLessons;
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
