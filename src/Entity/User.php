<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(nullable: true)]
    private ?string $username = null;

    #[ORM\Column(nullable: true)]
    private ?string $profileImage = null;

    #[ORM\Column(nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(nullable: true)]
    private ?string $socialLinks = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        if($this->getUsername()==null){
            $this->setUsernameFromEmail(); 
        }
        // Set username from email
        if($this->getProfileImage()==null){
            $this->setDefaultProfileImage(); 
        }
        
        $this->setDefaultBio();// Set default profile image if not set
        return $this;
    }

    private function setUsernameFromEmail(): void
    {
        // Extract username from email before '@' symbol
        $username = explode('@', $this->email)[0];
        $this->username = $username;
    }

    private function setDefaultProfileImage(): void
    {
        // Set default profile image URL if not set
        if (!$this->profileImage) {
            $this->profileImage = 'https://www.iprcenter.gov/image-repository/blank-profile-picture.png/@@images/image.png';
        }
    }
    private function setDefaultBio(): void
    {
        
        if (!$this->bio) {
            $this->bio = 'This is a sample bio the tutor please edit it ';
        }
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }
    

    public function getProfileImage(): ?string
    {
        return $this->profileImage;
    }

    public function setProfileImage(string $profileImage): static
    {
        $this->profileImage = $profileImage;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;
        return $this;
    }

    public function getSocialLinks(): ?string
    {
        return $this->socialLinks;
    }

    public function setSocialLinks(?string $socialLinks): static
    {
        $this->socialLinks = $socialLinks;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
