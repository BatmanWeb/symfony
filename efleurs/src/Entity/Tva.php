<?php

namespace App\Entity;

use App\Repository\TvaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TvaRepository::class)
 */
class Tva
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $tvaLabel;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     */
    private $tvaTaux;

    /**
     * @ORM\OneToMany(targetEntity=article::class, mappedBy="tva")
     */
    private $article;

    public function __construct()
    {
        $this->article = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTvaLabel(): ?string
    {
        return $this->tvaLabel;
    }

    public function setTvaLabel(string $tvaLabel): self
    {
        $this->tvaLabel = $tvaLabel;

        return $this;
    }

    public function getTvaTaux(): ?string
    {
        return $this->tvaTaux;
    }

    public function setTvaTaux(string $tvaTaux): self
    {
        $this->tvaTaux = $tvaTaux;

        return $this;
    }

    /**
     * @return Collection|article[]
     */
    public function getArticle(): Collection
    {
        return $this->article;
    }

    public function addArticle(article $article): self
    {
        if (!$this->article->contains($article)) {
            $this->article[] = $article;
            $article->setTva($this);
        }

        return $this;
    }

    public function removeArticle(article $article): self
    {
        if ($this->article->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getTva() === $this) {
                $article->setTva(null);
            }
        }

        return $this;
    }
}
