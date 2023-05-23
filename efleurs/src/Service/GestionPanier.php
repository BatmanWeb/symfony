<?php

namespace App\Service;
use app\Entity\Article;
use app\Entity\LigneCommande;


class GestionPanier {
    private $panier;
    private $message;
    private $signature;
    public function __construct(){
        $this->panier = [];
    }
    public function getPanier(){
        return $this->panier;
    }
    public function setPanier($panier){
        $this->panier = $panier;
        return $this;
    }
    public function ajouterArticle(Article $article) : void{
        if($article != null){
            $id = $article->getId();
            if(isset($this->panier[$id])){
                $ligneCmd = $this->panier[$id];
            $newQte = $ligneCmd->getLigneQte()+1;
            $ligneCmd->setLigneQte($newQte);
            }else{
                $ligneCmd = new LigneCommande();
                $ligneCmd->setArticle($article)
                ->setLigneQte(1)
                ->setLignePrixHt($article->getArtPrixHT())
                ->setLigneTva($article->getTva()->getTvaTaux())
                ;
            $this->panier[$id] = $ligneCmd;
            }
        }
    }
    public function getQteArticles(): int
    {
        $sum= 0;
        foreach($this->panier as $ligne){
            $sum += $ligne->getLigneQte();
        }
        return $sum;
    }
    public function getTotalHt(): float
    {
        $sum= 0;
        foreach($this->panier as $ligne){
            $sum += $ligne->getSousTotalHt();
        }
        return $sum;
    }
    public function getTotalTtc(): float
    {
        $sum= 0;
        foreach($this->panier as $ligne){
            $sum += $ligne->getSousTotalTtc();
        }
        return $sum;
    }
    public function getMontantTva(): float
    {
        return $this->getTotalTtc() - $this->getTotalHt();
    }
    public function remove(int $id) : void{
        unset($this->panier[$id]);
    } 
    public function diminuerArticle(int $id) : void
    {
        if($this->panier[$id] != null){
          $ligne = $this->panier[$id];
          $qte = $ligne->getLigneQte();
          if($qte == 1){
              unset($this->panier[$id]);
          }else {
              $ligne->setLigneQte($qte - 1);
          }
        }
    }
    public function getMessage (){
        return $this->message;
    }
    public function setMessage($message){
        $this->message=$message;
        return $this;
    }
    public function getSignature (){
        return $this->signature;
    }
    public function setSignature($signature){
        $this->signature=$signature;
        return $this;
    }

    public function reset(){
        $this->panier = [];
        $this->message = '';
        $this->signature = '';

    }
}