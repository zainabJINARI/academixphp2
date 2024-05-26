<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LessonRepository::class)]
class Lesson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column(length: 255)]
    private ?string $urlvideo = null;

    #[ORM\Column]
    private ?bool $isFiniched = false;


    #[ORM\Column]
    private ?int $idModule = null;


    #[ORM\Column(name: "lesson_order")]
    private ?int $order = null;

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

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getUrlvideo(): ?string
    {
        return $this->urlvideo;
    }

    public function setUrlvideo(string $urlvideo): static
    {
        $this->urlvideo = $urlvideo;

        return $this;
    }

    public function isIsFiniched(): ?bool
    {
        return $this->isFiniched;
    }

    public function setIsFiniched(bool $isFiniched): static
    {
        $this->isFiniched = $isFiniched;

        return $this;
    }



    public function getIdModule(): ?int
    {
        return $this->idModule;
    }

    public function setIdModule(int $idModule): static
    {
        $this->idModule = $idModule;
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

    
}