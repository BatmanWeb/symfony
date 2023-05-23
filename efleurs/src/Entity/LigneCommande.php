<?php

namespace App\Entity;

use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LigneCommandeRepository::class)
 */
class LigneCommande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2)
     */
    private $lignePrixHt;

    /**
     * @ORM\Column(type="integer")
     */
    private $ligneQte;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $ligneTva;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="ligneCommandes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $article;

    /**
     * @ORM\ManyToOne(targetEntity=Commande::class, inversedBy="lignes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commande;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLignePrixHt(): ?string
    {
        return $this->lignePrixHt;
    }

    public function setLignePrixHt(string $lignePrixHt): self
    {
        $this->lignePrixHt = $lignePrixHt;

        return $this;
    }

    public function getLigneQte(): ?int
    {
        return $this->ligneQte;
    }

    public function setLigneQte(int $ligneQte): self
    {
        $this->ligneQte = $ligneQte;

        return $this;
    }

    public function getLigneTva(): ?string
    {
        return $this->ligneTva;
    }

    public function setLigneTva(string $ligneTva): self
    {
        $this->ligneTva = $ligneTva;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }
    public function getSousTotalHt() : float {
        return $this->ligneQte * $this->lignePrixHt;
    }
    public function getSousTotalTtc() : float{
        return $this->getSousTotalHt() *  (1 + $this->ligneTva / 100);
    }
}
