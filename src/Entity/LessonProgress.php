<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class LessonProgress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Lesson::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lesson $lesson = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $student = null;

    #[ORM\ManyToOne(targetEntity: ModuleProgress::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?ModuleProgress $moduleProgress = null;

    #[ORM\Column]
    private ?bool $completed = false;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getModuleProgress(): ?ModuleProgress
    {
        return $this->moduleProgress;
    }

    public function setModuleProgress(ModuleProgress $moduleProgress): static
    {
        $this->moduleProgress = $moduleProgress;
        return $this;
    }

    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(Lesson $lesson): static
    {
        $this->lesson = $lesson;
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
