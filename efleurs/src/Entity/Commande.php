<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="datetime")
     */
    private $cmdDate;
    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $cmdMessage;
    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $cmdSignature;
    /**
     * @ORM\OneToMany(targetEntity=LigneCommande::class, mappedBy="commande")
     */
    private $lignes;
    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    public function __construct()
    {
        $this->lignes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCmdDate(): ?\DateTimeInterface
    {
        return $this->cmdDate;
    }

    public function setCmdDate(\DateTimeInterface $cmdDate): self
    {
        $this->cmdDate = $cmdDate;

        return $this;
    }

    public function getCmdMessage(): ?string
    {
        return $this->cmdMessage;
    }

    public function setCmdMessage(?string $cmdMessage): self
    {
        $this->cmdMessage = $cmdMessage;

        return $this;
    }

    public function getCmdSignature(): ?string
    {
        return $this->cmdSignature;
    }

    public function setCmdSignature(?string $cmdSignature): self
    {
        $this->cmdSignature = $cmdSignature;

        return $this;
    }

    /**
     * @return Collection|LigneCommande[]
     */
    public function getLignes(): Collection
    {
        return $this->lignes;
    }

    public function addLigne(LigneCommande $ligne): self
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes[] = $ligne;
            $ligne->setCommande($this);
        }

        return $this;
    }

    public function removeLigne(LigneCommande $ligne): self
    {
        if ($this->lignes->removeElement($ligne)) {
            // set the owning side to null (unless already changed)
            if ($ligne->getCommande() === $this) {
                $ligne->setCommande(null);
            }
        }

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
