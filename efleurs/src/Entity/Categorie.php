<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 */
class Categorie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $catNom;

    /**
     * @ORM\Column(type="boolean")
     */
    private $catActif;

    /**
     * @ORM\Column(type="integer")
     */
    private $catOrdre;

    /**
     * @ORM\ManyToMany(targetEntity=Article::class, mappedBy="categorie")
     */
    private $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCatNom(): ?string
    {
        return $this->catNom;
    }

    public function setCatNom(string $catNom): self
    {
        $this->catNom = $catNom;

        return $this;
    }

    public function getCatActif(): ?bool
    {
        return $this->catActif;
    }

    public function setCatActif(bool $catActif): self
    {
        $this->catActif = $catActif;

        return $this;
    }

    public function getCatOrdre(): ?int
    {
        return $this->catOrdre;
    }

    public function setCatOrdre(int $catOrdre): self
    {
        $this->catOrdre = $catOrdre;

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->addCategorie($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            $article->removeCategorie($this);
        }

        return $this;
    }
}
