<?php

namespace App\Entity;

use DateTimeImmutable;
use Symfony\Component\Uid\Uuid;

class Image
{

    private Uuid $id;
    private string $name;
    private string $path;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        Uuid $id,
        string $name,
        string $path,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->path = $path;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
