<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     * @Groups("api_userlists_browse")
     * @Groups("api_userlists_read")
     * 
     * @Groups("api_users_new")
     * 
     *  @Groups("api_userlists_POST")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * 
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     * @Groups("api_userlists_browse")
     * @Groups("api_userlists_read")
     * 
     * @Groups("api_users_new")
     * 
     * @Groups("api_userlists_POST")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     * 
     * @Groups("api_users_new")
     */
    private $userNickname;
    
    /**
     * @ORM\Column(type="integer")
     * 
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     */
    private $verified;

    /**
     * @ORM\Column(type="datetime_immutable")
     * 
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * 
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=UserList::class, mappedBy="users")
     * 
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     * 
     */
    private $userlist;



    public function __construct()
    {        
        $this->updatedAt = new DateTimeImmutable();
        $this->createdAt = new DateTimeImmutable();
        $this->userlist = new ArrayCollection();
    }

    public function __toString() {
        return $this->userNickname;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
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

    public function setRoles(array $roles): self
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

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|UserList[]
     */
    public function getUserlist(): Collection
    {
        return $this->userlist;
    }

    public function addUserlist(UserList $userlist): self
    {
        if (!$this->userlist->contains($userlist)) {
            $this->userlist[] = $userlist;
            $userlist->setUsers($this);
        }

        return $this;
    }

    public function removeUserlist(UserList $userlist): self
    {
        if ($this->userlist->removeElement($userlist)) {
            // set the owning side to null (unless already changed)
            if ($userlist->getUsers() === $this) {
                $userlist->setUsers(null);
            }
        }

        return $this;
    }

    public function getUserNickname(): ?string
    {
        return $this->userNickname;
    }

    public function setUserNickname(string $userNickname): self
    {
        $this->userNickname = $userNickname;

        return $this;
    }

    public function getVerified(): ?int
    {
        return $this->verified;
    }

    public function setVerified(int $verified): self
    {
        $this->verified = $verified;

        return $this;
    }
}
