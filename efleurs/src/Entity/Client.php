<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client implements PasswordAuthenticatedUserInterface
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
    private $cliNom;
    /**
     * @ORM\Column(type="string", length=50)
     */
    private $cliPrenom;
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $cliEmail;
    /**
     * @ORM\Column(type="string", length=150)
     */
    private $cliMdp;

    /**
     * @ORM\OneToMany(targetEntity=Commande::class, mappedBy="client")
     */
    private $commandes;

    public function __construct()
    {
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCliNom(): ?string
    {
        return $this->cliNom;
    }

    public function setCliNom(string $cliNom): self
    {
        $this->cliNom = $cliNom;

        return $this;
    }

    public function getCliPrenom(): ?string
    {
        return $this->cliPrenom;
    }

    public function setCliPrenom(string $cliPrenom): self
    {
        $this->cliPrenom = $cliPrenom;

        return $this;
    }

    public function getCliEmail(): ?string
    {
        return $this->cliEmail;
    }

    public function setCliEmail(string $cliEmail): self
    {
        $this->cliEmail = $cliEmail;

        return $this;
    }

    public function getCliMdp(): ?string
    {
        return $this->cliMdp;
    }

    public function setCliMdp(string $cliMdp): self
    {
        $this->cliMdp = $cliMdp;

        return $this;
    }

/* code imposé par PasswordAuthenticatedUserInterface*/
public function getPassword(): string{
    return $this->cliMdp;
}

/* fin du code imposé par PasswordAuthenticatedUserInterface */

/**
 * @return Collection|Commande[]
 */
public function getCommandes(): Collection
{
    return $this->commandes;
}

public function addCommande(Commande $commande): self
{
    if (!$this->commandes->contains($commande)) {
        $this->commandes[] = $commande;
        $commande->setClient($this);
    }

    return $this;
}

public function removeCommande(Commande $commande): self
{
    if ($this->commandes->removeElement($commande)) {
        // set the owning side to null (unless already changed)
        if ($commande->getClient() === $this) {
            $commande->setClient(null);
        }
    }

    return $this;
}
}
