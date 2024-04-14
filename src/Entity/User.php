<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\UserRole ;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $fullname = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 10)]
    private ?string $password = null;

    #[ORM\Column(length: 20)]
    private ?string $role = UserRole::STUDENT;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default": "default_profile.jpg"})
     */
    #[ORM\Column(length: 255)]
    private $profilePhoto = 'https://stock.adobe.com/fr/images/default-avatar-profile-flat-icon-social-media-user-vector-portrait-of-unknown-a-human-image/353110097';

    // Other properties and methods...

    public function getProfilePhoto(): ?string
    {
        return $this->profilePhoto;
    }

    public function setProfilePhoto(?string $profilePhoto): self
    {
        $this->profilePhoto = $profilePhoto;

        return $this;
    }

    #[ORM\OneToMany(mappedBy: 'tutor', targetEntity: Course::class)]
    private Collection $courses;


    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): static
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }
    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setTutor($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getTutor() === $this) {
                $course->setTutor(null);
            }
        }

        return $this;
    }

    

 
}
