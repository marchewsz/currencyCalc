<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecordRepository")
 */
class Record
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pln_value;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $foreign_currency_code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $foreign_value;

    /**
     * @ORM\Column(type="datetime")
     */
    private $generation_date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $city;

    public function getId()
    {
        return $this->id;
    }

    public function getPlnValue()
    {
        return $this->pln_value;
    }

    public function setPlnValue(string $pln_value)
    {
        $this->pln_value = $pln_value;

        return $this;
    }

    public function getForeignCurrencyCode()
    {
        return $this->foreign_currency_code;
    }

    public function setForeignCurrencyCode(string $foreign_currency_code)
    {
        $this->foreign_currency_code = $foreign_currency_code;

        return $this;
    }

    public function getForeignValue()
    {
        return $this->foreign_value;
    }

    public function setForeignValue(string $foreign_value)
    {
        $this->foreign_value = $foreign_value;

        return $this;
    }

    public function getGenerationDate()
    {
        return $this->generation_date;
    }

    public function setGenerationDate(\DateTimeInterface $generation_date)
    {
        $this->generation_date = $generation_date;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }
}
