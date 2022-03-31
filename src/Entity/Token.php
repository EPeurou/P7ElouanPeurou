<?php

namespace App\Entity;

use App\Repository\TokenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TokenRepository::class)
 */
class Token
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $tokenstr;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTokenstr(): ?string
    {
        return $this->tokenstr;
    }

    public function setTokenstr(?string $tokenstr): self
    {
        $this->tokenstr = $tokenstr;

        return $this;
    }
}
