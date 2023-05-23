<?php

namespace App\Controller;



use App\Repository\CommandeRepository;
use App\Entity\Commande;
use App\Repository\ClientRepository;
use App\Repository\ArticleRepository;
use App\Repository\LigneCommandeRepository;
use App\Service\GestionPanier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{

    private $articlesDAO;
    private $clientDAO;
    private $commandeDAO;
    private $ligneDAO;

    public function __construct(LigneCommandeRepository $ligneDAO,ArticleRepository $articlesDAO, ClientRepository $clientDAO ,CommandeRepository $commandeDAO ){
        $this->articlesDAO = $articlesDAO;
        $this->clientDAO = $clientDAO;
        $this->commandeDAO = $commandeDAO;
        $this->ligneDAO = $ligneDAO;
    }
    /**
     * @Route("/panier/detail", name="panier-detail")
     */
    public function afficherPanier(Request $request): Response
    {
        //récupère la session
        $session = $request->getSession();
        $gestionPanier = $session->get('gestionPanier', new GestionPanier()); // on recup une instant de gestion panier
        $session->set('gestionPanier', $gestionPanier);
        $panier = $gestionPanier->getPanier();
        return $this->render('panier/panier.html.twig', compact('panier'));
    }
        /**
     * @Route("/commander/message", name="cmd-detail")
     */
    public function afficherMessageForm(Request $request): Response
    {
        $session = $request->getSession();
        //on verifie si l'user et connecter
        $client = $session->get('user');
        if($client == null){
            return  $this->reDirectToRoute(('client-login-form'));
        }
        $gestionPanier = $session->get('gestionPanier', new GestionPanier()); // on recup une instant de gestion panier
        $session->set('gestionPanier', $gestionPanier);
        $message = $gestionPanier->getMessage();
        $signature = $gestionPanier->getSignature();
        return $this->render('panier/message-form.html.twig', compact('message','signature'));
    }
        /**
     * @Route("/commander/enregistrer/message", name="cmd-reg-detail")
     */
    public function enregistrerMessageForm(Request $request): Response
    {
             //récupère la session
        $session = $request->getSession();
        $gestionPanier = $session->get('gestionPanier', new GestionPanier()); // on recup une instant de gestion panier
        $session->set('gestionPanier', $gestionPanier);
        $message = $request->request->get('message');//recup msg form
        $signature = $request->request->get('signature');//recup signature form
        $gestionPanier->setMessage($message);
        $gestionPanier->setSignature($signature);
        $totalTransac = $gestionPanier->getTotalTtc();
        return $this->render('panier/payement-form.html.twig', compact('totalTransac'));
    }
        /**
     * @Route("/panier/ajouter/{id}", name="panier-add")
     */
    public function ajouterArticle($id, Request $request): Response
    {
        //récupère la session
        $session = $request->getSession();
        $gestionPanier = $session->get('gestionPanier', new GestionPanier()); // on recup une instant de gestion panier
        $session->set('gestionPanier', $gestionPanier);
        $articles = $this->articlesDAO->findByIdAndActif($id,true);
        $gestionPanier->ajouterArticle($articles);
        //on fait une redirection pour quand on actualise la page ca ne rajoute pas des articles
        return $this->redirectToRoute('panier-detail');
    }

    public function afficheResum(Request $request){
                //récupère la session
                $session = $request->getSession();
                $gestionPanier = $session->get('gestionPanier', new GestionPanier()); // on recup une instant de gestion panier
                $session->set('gestionPanier', $gestionPanier);
                //qteArticles totalHT totalTva TotalTtc
                $qteArticles = $gestionPanier->getQteArticles();
                $totalHt = $gestionPanier->getTotalHt();
                $totalTva = $gestionPanier->getMontantTva();
                $totalTtc = $gestionPanier->getTotalTtc();
                return $this->render('panier/panier-resume.html.twig', compact('qteArticles','totalHt','totalTva','totalTtc'));
    }
        /**
     * @Route("/panier/supprimer/{id}", name="panier-supprimer")
     */
    public function remove($id, Request $request): Response
    {
        //récupère la session
        $session = $request->getSession();
        $gestionPanier = $session->get('gestionPanier', new GestionPanier()); //on récupère une instance de GestionPanier
        $session->set('gestionPanier', $gestionPanier);

        $gestionPanier->remove($id);
        //on fait une redirection pour le F5( quand le user rafraichit son navigateur, on ne doit pas ajouter des produits)
        //la redirection n'ajoutera pas des articles dans son panier
        return $this->redirectToRoute('panier-detail',);
    }
            /**
     * @Route("/panier/diminuer/{id}", name="panier-diminuer")
     */
    public function diminuerArticle($id, Request $request): Response
    {
                //récupère la session
                $session = $request->getSession();
                $gestionPanier = $session->get('gestionPanier', new GestionPanier()); // on recup une instant de gestion panier
                $session->set('gestionPanier', $gestionPanier);
        
        
                $gestionPanier->diminuerArticle($id);
        
                //on fait une redirection pour quand on actualise la page ca ne rajoute pas des articles
                return $this->redirectToRoute('panier-detail');
    }
               /**
     * @Route("/commander/payer", name="panier-payer")
     */
    public function confirmation(Request $request): Response
    {
                //récupère la session
                $session = $request->getSession();
                $client = $session->get('user');
                // verifier que le client et tjr co
                if($client == null){
                    return $this->render('client/login-form.html.twig', []);
                }
                $idClient = $client->getId();
                

                $gestionPanier = $session->get('gestionPanier', new GestionPanier()); // on recup une instant de gestion panier
                $session->set('gestionPanier', $gestionPanier);

                $message = $gestionPanier->getMessage();
                $signature = $gestionPanier->getSignature();
                $panier = $gestionPanier->getPanier();
            //verifier que le panier n'est pas vide
            if(count($panier) == 0){
                return $this->redirectToRoute('panier-detail');
            }

                //TODO crée une commande
                $client = $this->clientDAO->find($idClient); //rechercher le client dans la bdd
                $cmd = new Commande();
                $cmd->setCmdDate(new \DateTime())
                    ->setCmdMessage($message)   
                    ->setCmdSignature($signature)
                    ->setClient($client);
                    $this->commandeDAO->sauv($cmd);
        // crée et sauvegarder les lignes de commande

        foreach($panier as $idArt => $lignes){
            $lignes->setCommande($cmd);
            $plante = $this->articlesDAO->find($idArt);
            $lignes->setArticle($plante);
            $this->ligneDAO->sauv($lignes);

        }
                
                //on reset le panier
            $gestionPanier->reset();

                $message="merci pour votre achat";

            
                //on fait une redirection pour quand on actualise la page ca ne rajoute pas des articles
                return $this->render('home/confirmation.html.twig', compact('message'));
    }
}
