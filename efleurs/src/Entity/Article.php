<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $artPrixHT;
    /**
     * @ORM\Column(type="string", length=150)
     */
    private $artNom;
    /**
     * @ORM\Column(type="text")
     */
    private $artDescription;
    /**
     * @ORM\Column(type="string", length=90)
     */
    private $artImage;
    /**
     * @ORM\Column(type="boolean")
     */
    private $artActif;

    /**
     * @ORM\ManyToOne(targetEntity=Tva::class, inversedBy="article")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tva;

    /**
     * @ORM\ManyToMany(targetEntity=Categorie::class, inversedBy="articles")
     */
    private $categorie;

    /**
     * @ORM\OneToMany(targetEntity=LigneCommande::class, mappedBy="article")
     */
    private $ligneCommandes;

    public function __construct()
    {
        $this->categorie = new ArrayCollection();
        $this->ligneCommandes = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArtPrixHT(): ?string
    {
        return $this->artPrixHT;
    }

    public function setArtPrixHT(string $artPrixHT): self
    {
        $this->artPrixHT = $artPrixHT;

        return $this;
    }

    public function getArtNom(): ?string
    {
        return $this->artNom;
    }

    public function setArtNom(string $artNom): self
    {
        $this->artNom = $artNom;

        return $this;
    }

    public function getArtDescription(): ?string
    {
        return $this->artDescription;
    }

    public function setArtDescription(string $artDescription): self
    {
        $this->artDescription = $artDescription;

        return $this;
    }

    public function getArtImage(): ?string
    {
        return $this->artImage;
    }

    public function setArtImage(string $artImage): self
    {
        $this->artImage = $artImage;

        return $this;
    }

    public function getArtActif(): ?bool
    {
        return $this->artActif;
    }

    public function setArtActif(bool $artActif): self
    {
        $this->artActif = $artActif;

        return $this;
    }

    public function getTva(): ?Tva
    {
        return $this->tva;
    }

    public function setTva(?Tva $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    /**
     * @return Collection|categorie[]
     */
    public function getCategorie(): Collection
    {
        return $this->categorie;
    }

    public function addCategorie(categorie $categorie): self
    {
        if (!$this->categorie->contains($categorie)) {
            $this->categorie[] = $categorie;
        }

        return $this;
    }

    public function removeCategorie(categorie $categorie): self
    {
        $this->categorie->removeElement($categorie);

        return $this;
    }
    public function getPrixTTC() : float{
        return $this->artPrixHT * (1 + $this->tva->getTvaTaux() / 100);
    }

    /**
     * @return Collection|LigneCommande[]
     */
    public function geLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): self
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes[] = $ligneCommande;
            $ligneCommande->setArticle($this);
        }

        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): self
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            // set the owning side to null (unless already changed)
            if ($ligneCommande->getArticle() === $this) {
                $ligneCommande->setArticle(null);
            }
        }

        return $this;
    } 
}
