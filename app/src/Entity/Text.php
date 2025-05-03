<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;
use Doctrine\ORM\Mapping as ORM;

#[Entity()]
#[Table(name: 'texts')]
class Text
{
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.ulid_generator')]
    private Ulid $id;

    #[ORM\Column(type: 'text')]
    private string $text;
    #[OneToOne(targetEntity: Image::class)]
    #[JoinColumn(name: 'image_id', referencedColumnName: 'id')]
    private Image $image;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    public function __construct(
        Ulid $id,
        string $text,
        Image $image,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->text = $text;
        $this->image = $image;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getImage(): Image
    {
        return $this->image;
    }
}
