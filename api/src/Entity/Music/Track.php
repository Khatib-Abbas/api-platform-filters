<?php

namespace App\Entity\Music;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Common\Filter\DateFilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\ApiPlatform\Filter\StringFilter\SearchStringFilter;
use App\Repository\Music\TrackRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;

#[ApiResource]
#[ORM\Entity(repositoryClass: TrackRepository::class)]
##[ApiFilter(SearchStringFilter::class, properties: ['name' => 'exact','label'=>'partial'])]
#[ApiFilter(DateFilter::class, properties: ['createdAt'=> DateFilterInterface::EXCLUDE_NULL])]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'exact'])]

class Track
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private $name;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private $label="ds";

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: false)]
    private $createdAt;


    #[ORM\Column(type: Types::INTEGER)]
    private $duration;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param mixed $duration
     */
    public function setDuration($duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label): void
    {
        $this->label = $label;
    }


}
