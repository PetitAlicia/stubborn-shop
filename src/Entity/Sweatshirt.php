<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Sweatshirt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $name;

    #[ORM\Column(type: 'float')]
    private float $price;

    #[ORM\Column(type: 'integer')]
    private int $stock_xs;

    #[ORM\Column(type: 'integer')]
    private int $stock_s;

    #[ORM\Column(type: 'integer')]
    private int $stock_m;

    #[ORM\Column(type: 'integer')]
    private int $stock_l;

    #[ORM\Column(type: 'integer')]
    private int $stock_xl;

    #[ORM\Column(type: 'boolean')]
    private bool $featured;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getStockXs(): int
    {
        return $this->stock_xs;
    }

    public function setStockXs(int $stock_xs): self
    {
        $this->stock_xs = $stock_xs;
        return $this;
    }

    public function getStockS(): int
    {
        return $this->stock_s;
    }

    public function setStockS(int $stock_s): self
    {
        $this->stock_s = $stock_s;
        return $this;
    }

    public function getStockM(): int
    {
        return $this->stock_m;
    }

    public function setStockM(int $stock_m): self
    {
        $this->stock_m = $stock_m;
        return $this;
    }

    public function getStockL(): int
    {
        return $this->stock_l;
    }

    public function setStockL(int $stock_l): self
    {
        $this->stock_l = $stock_l;
        return $this;
    }

    public function getStockXl(): int
    {
        return $this->stock_xl;
    }

    public function setStockXl(int $stock_xl): self
    {
        $this->stock_xl = $stock_xl;
        return $this;
    }

    public function isFeatured(): bool
    {
        return $this->featured;
    }

    public function setFeatured(bool $featured): self
    {
        $this->featured = $featured;
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

    public function getStockBySize(string $size): int
    {
        return match (strtolower($size)) {
            'xs' => $this->getStockXs(),
            's' => $this->getStockS(),
            'm' => $this->getStockM(),
            'l' => $this->getStockL(),
            'xl' => $this->getStockXl(),
            default => 0,
        };
    }

}
