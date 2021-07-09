<?php

namespace App\Entity;

use App\Repository\PrizeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PrizeRepository::class)
 */
class Prize
{
    public const TYPE_MONEY = 'money';
    public const TYPE_POINTS = 'points';
    public const TYPE_PHYSICAL = 'physical';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isConvertable = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $convertionRate;

    /**
     * @ORM\Column(type="integer")
     */
    private $amountFrom;

    /**
     * @ORM\Column(type="integer")
     */
    private $amountTo;

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="prize")
     */
    private $games;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    public function __construct()
    {
        $this->games = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIsConvertable(): ?bool
    {
        return $this->isConvertable;
    }

    public function setIsConvertable(bool $isConvertable): self
    {
        $this->isConvertable = $isConvertable;

        return $this;
    }

    public function getConvertionRate(): ?int
    {
        return $this->convertionRate;
    }

    public function setConvertionRate(?int $convertionRate): self
    {
        $this->convertionRate = $convertionRate;

        return $this;
    }

    public function getAmountFrom(): ?int
    {
        return $this->amountFrom;
    }

    public function setAmountFrom(int $amountFrom): self
    {
        $this->amountFrom = $amountFrom;

        return $this;
    }

    public function getAmountTo(): ?int
    {
        return $this->amountTo;
    }

    public function setAmountTo(int $amountTo): self
    {
        $this->amountTo = $amountTo;

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setPrize($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getPrize() === $this) {
                $game->setPrize(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
