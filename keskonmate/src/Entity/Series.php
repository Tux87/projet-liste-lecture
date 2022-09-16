<?php

namespace App\Entity;

use App\Repository\SeriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=SeriesRepository::class)
 */
class Series
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @Groups("api_actors_browse")
     * @Groups("api_actors_read")    
     * @Groups("api_genres_browse")
     * @Groups("api_genres_read")
     * @Groups("api_seasons_browse")
     * @Groups("api_seasons_read")
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     * @Groups("api_userlists_browse")
     * @Groups("api_userlists_read")
     * 
     *  @Groups("api_userlists_POST")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Groups("api_actors_browse")
     * @Groups("api_actors_read")
     * @Groups("api_genres_browse")
     * @Groups("api_genres_read")
     * @Groups("api_seasons_browse")
     * @Groups("api_seasons_read")
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     * @Groups("api_userlists_browse")
     * @Groups("api_userlists_read")
     * 
     *  @Groups("api_userlists_POST")
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * 
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     */
    private $synopsis;

    /**
     * @ORM\Column(type="date", nullable=true)
     * 
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     */
    private $releaseDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     */
    private $director;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     */
    private $numberOfSeasons;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     */
    private $homeOrder;

    /**
     * @ORM\Column(type="datetime_immutable")
     * 
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * 
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=UserList::class, mappedBy="series")
     * 
     */
    private $userlist;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, inversedBy="series", cascade={"persist"})
     * 
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     */
    private $genre;

    /**
     * @ORM\OneToMany(targetEntity=Season::class, mappedBy="series")
     * 
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     */
    private $season;

    /**
     * @ORM\ManyToMany(targetEntity=Actor::class, inversedBy="series", cascade={"persist"})
     * 
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     */
    private $actor;

    public function __construct()
    {
        $this->userlist = new ArrayCollection();
        $this->genre = new ArrayCollection();
        $this->season = new ArrayCollection();
        $this->actor = new ArrayCollection();
    }

    public function __toString() 
    {
        return $this->title . ' (' . $this->id . ')';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDirector(): ?string
    {
        return $this->director;
    }

    public function setDirector(string $director): self
    {
        $this->director = $director;

        return $this;
    }

    public function getNumberOfSeasons(): ?int
    {
        return $this->numberOfSeasons;
    }

    public function setNumberOfSeasons(int $numberOfSeasons): self
    {
        $this->numberOfSeasons = $numberOfSeasons;

        return $this;
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
        }

        return $this;
    }

    public function removeUserlist(UserList $userlist): self
    {
        $this->userlist->removeElement($userlist);

        return $this;
    }

    /**
     * @return Collection|Genre[]
     */
    public function getGenre(): Collection
    {
        return $this->genre;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genre->contains($genre)) {
            $this->genre[] = $genre;
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        $this->genre->removeElement($genre);

        return $this;
    }

    /**
     * @return Collection|Season[]
     */
    public function getSeason(): Collection
    {
        return $this->season;
    }

    public function addSeason(Season $season): self
    {
        if (!$this->season->contains($season)) {
            $this->season[] = $season;
            $season->setSeries($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): self
    {
        if ($this->season->removeElement($season)) {
            // set the owning side to null (unless already changed)
            if ($season->getSeries() === $this) {
                $season->setSeries(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Actor[]
     */
    public function getActor(): Collection
    {
        return $this->actor;
    }

    public function addActor(Actor $actor): self
    {
        if (!$this->actor->contains($actor)) {
            $this->actor[] = $actor;
        }

        return $this;
    }

    public function removeActor(Actor $actor): self
    {
        $this->actor->removeElement($actor);

        return $this;
    }

    public function getHomeOrder(): ?int
    {
        return $this->homeOrder;
    }

    public function setHomeOrder(?int $homeOrder): self
    {
        $this->homeOrder = $homeOrder;

        return $this;
    }
}
