<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=GenreRepository::class)
 * 
 * @UniqueEntity(fields={"name"})
 */
class Genre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @Groups("api_genres_browse")
     * @Groups("api_genres_read")
     * @Groups("api_seasons_browse")
     * @Groups("api_seasons_read")
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128, unique=true)
     * 
     * @Groups("api_genres_browse")
     * @Groups("api_genres_read")
     * @Groups("api_seasons_browse")
     * @Groups("api_seasons_read")
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     */
    private $name;

    /**
     * @ORM\Column(type="datetime_immutable")
     * 
     * @Groups("api_genres_browse")
     * @Groups("api_genres_read")
     * @Groups("api_seasons_browse")
     * @Groups("api_seasons_read")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * 
     * @Groups("api_genres_browse")
     * @Groups("api_genres_read")
     * @Groups("api_seasons_browse")
     * @Groups("api_seasons_read")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=Series::class, mappedBy="genre", cascade={"persist"})
     * 
     * @Groups("api_genres_browse")
     * @Groups("api_genres_read")
     */
    private $series;

    public function __construct()
    {
        $this->series = new ArrayCollection();
    }

    public function __toString() 
    {
        return $this->name . ' (' . $this->id . ')';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
     * @return Collection|Series[]
     */
    public function getSeries(): Collection
    {
        return $this->series;
    }

    public function addSeries(Series $series): self
    {
        if (!$this->series->contains($series)) {
            $this->series[] = $series;
            $series->addGenre($this);
        }

        return $this;
    }

    public function removeSeries(Series $series): self
    {
        if ($this->series->removeElement($series)) {
            $series->removeGenre($this);
        }

        return $this;
    }
}
