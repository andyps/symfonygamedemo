<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    public const STATUS_LOST = 'lost';
    public const STATUS_WIN = 'win';
    public const STATUS_REJECTED = 'rejected'; // rejected by user
    public const STATUS_APPROVED = 'approved'; // approved by user
    public const STATUS_PROCESSED = 'processed';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $points;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isConvertedToPoints = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $convertionRate;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity=Prize::class, inversedBy="games")
     * @ORM\JoinColumn(nullable=true)
     */
    private $prize;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(?int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getIsConvertedToPoints(): ?bool
    {
        return $this->isConvertedToPoints;
    }

    public function setIsConvertedToPoints(bool $isConvertedToPoints): self
    {
        $this->isConvertedToPoints = $isConvertedToPoints;

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

    public function getPlayer(): ?User
    {
        return $this->player;
    }

    public function setPlayer(?User $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getPrize(): ?Prize
    {
        return $this->prize;
    }

    public function setPrize(?Prize $prize): self
    {
        $this->prize = $prize;

        return $this;
    }

    public function approve()
    {
        if ($this->isLost()) {
            return;
        }
        $this->setStatus(Game::STATUS_APPROVED);
    }

    public function reject()
    {
        if ($this->isLost()) {
            return;
        }
        $this->setStatus(Game::STATUS_REJECTED);
    }

    public function isLost(): bool
    {
        return $this->status === self::STATUS_LOST;
    }
}
