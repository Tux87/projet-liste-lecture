<?php

namespace App\Entity;

use App\Repository\SeasonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=SeasonRepository::class)
 */
class Season
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @Groups("api_seasons_browse")
     * @Groups("api_seasons_read")
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @Groups("api_seasons_browse")
     * @Groups("api_seasons_read")
     * @Groups("api_series_browse")
     * @Groups("api_series_read")
     * @Groups("api_users_browse")
     * @Groups("api_users_read")
     */
    private $seasonNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @Groups("api_seasons_browse")
     * @Groups("api_seasons_read")
     */
    private $numberOfEpisodes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups("api_seasons_browse")
     * @Groups("api_seasons_read")
     */
    private $image;

    /**
     * @ORM\Column(type="datetime_immutable")
     * 
     * @Groups("api_seasons_browse")
     * @Groups("api_seasons_read")
     * 
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * 
     * @Groups("api_seasons_browse")
     * @Groups("api_seasons_read")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Series::class, inversedBy="season")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * 
     * @Groups("api_seasons_browse")
     * @Groups("api_seasons_read")
     */
    private $series;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeasonNumber(): ?int
    {
        return $this->seasonNumber;
    }

    public function setSeasonNumber(?int $seasonNumber): self
    {
        $this->seasonNumber = $seasonNumber;

        return $this;
    }

    public function getNumberOfEpisodes(): ?int
    {
        return $this->numberOfEpisodes;
    }

    public function setNumberOfEpisodes(?int $numberOfEpisodes): self
    {
        $this->numberOfEpisodes = $numberOfEpisodes;

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

    public function getSeries(): ?Series
    {
        return $this->series;
    }

    public function setSeries(?Series $series): self
    {
        $this->series = $series;

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
}
