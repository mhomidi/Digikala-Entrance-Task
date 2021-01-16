<?php

namespace App\Entity;

use App\Repository\InfoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InfoRepository::class)
 */
class Info
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=12)
     */
    private $number;

    /**
     * @ORM\Column(type="integer")
     */
    private $allSent;

    /**
     * @ORM\Column(type="integer")
     */
    private $firstAPISent;

    /**
     * @ORM\Column(type="integer")
     */
    private $firstAPIFail;

    /**
     * @ORM\Column(type="integer")
     */
    private $secondAPISent;

    /**
     * @ORM\Column(type="integer")
     */
    private $secondAPIFail;

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getAllSent(): ?int
    {
        return $this->allSent;
    }

    public function setAllSent(int $allSent): self
    {
        $this->allSent = $allSent;

        return $this;
    }

    public function getFirstAPISent(): ?int
    {
        return $this->firstAPISent;
    }

    public function setFirstAPISent(int $firstAPISent): self
    {
        $this->firstAPISent = $firstAPISent;

        return $this;
    }

    public function getFirstAPIFail(): ?int
    {
        return $this->firstAPIFail;
    }

    public function setFirstAPIFail(int $firstAPIFail): self
    {
        $this->firstAPIFail = $firstAPIFail;

        return $this;
    }

    public function getSecondAPISent(): ?int
    {
        return $this->secondAPISent;
    }

    public function setSecondAPISent(int $secondAPISent): self
    {
        $this->secondAPISent = $secondAPISent;

        return $this;
    }

    public function getSecondAPIFail(): ?int
    {
        return $this->secondAPIFail;
    }

    public function setSecondAPIFail(int $secondAPIFail): self
    {
        $this->secondAPIFail = $secondAPIFail;

        return $this;
    }

    public function __construct(string $number)
    {
        $this->number = $number;
        $this->allSent = 0;
        $this->firstAPIFail = 0;
        $this->firstAPISent = 0;
        $this->secondAPISent = 0;
        $this->secondAPIFail = 0;
    }
}
