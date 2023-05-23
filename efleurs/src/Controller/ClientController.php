<?php

namespace App\Controller;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{

    private $clientDAO;
    private $pwdEncoder;



    public function __construct(clientRepository $clientDAO, UserPasswordHasherInterface $pwdEncoder){
        $this->clientDAO =$clientDAO;
        $this->pwdEncoder =$pwdEncoder;
    }


    /**
     * @Route("/client/inscription/formulaire", name="client-inscription-form")
     */
    public function AfficherInscription(): Response
    {
        return $this->render('client/inscription-form.html.twig', [
        
        ]);
    }

        /**
     * @Route("/client/login/formulaire", name="client-login-form")
     */
    public function AfficherLogin(): Response
    {
        return $this->render('client/login-form.html.twig', [
        
        ]);
    }

    /**
     * @Route("/client/quitter", name="client-quitter")
     */
    public function quitter(Request $request): Response
    {
        $session = $request->getSession();
        $session->set('user', null);
        return $this->render('home/confirmation.html.twig', [
            'message' => 'Vous vous êtes déconnecté' ,
        ]);
    }


        /**
     * @Route("/client/login/valider", name="client-valider-login")
     */
    public function LoginValider(Request $request): Response
    {
        $email = $request->request->get('email');
        $email = trim($email);

        $mdp = $request->request->get('mdp');

        $erreurs=[];

        if(empty($email)){
            $erreurs['erreurEmail'] = "email obligatoire";
        }

        if(empty($mdp)){
            $erreurs['erreurMdp'] = "mot de passe obligatoire";
        }

        if($erreurs){
            $erreurs['email']=$email;
            return $this->render('client/login-form.html.twig', $erreurs);
        }


        $client = $this->clientDAO->trouverParEmail($email);
        $found=true;

        if($client){
            //TODO comment comparer les mdp
             $ok= $this->pwdEncoder->isPasswordValid($client,$mdp);
             if($ok){
                 $session= $request->getSession(); //L'objet session permet de suivre le navigateur
                 $session->set('user', $client);
                 return $this->render('home/confirmation.html.twig', [
                    'message' => 'ravis de vous revoir'. ' ' . $client->getCliNom() . ' ' . $client->getCliPrenom()
                ]);
             }else {
                 $found = false;
             }
        }else{
            $found=false;
        }
        if(!$found){
            return $this->render('client/login-form.html.twig',[
                "loginFailed" => "Erreur sur l'email ou le mot de passe",
                'email' => $email
            ]);
        }
        /*return $this->render('client/login-form.html.twig', [

        ]);*/
    }

    /**
     * @Route("/client/inscription/enregistrer", name="client-inscription-reg")
     */
    public function Enregistrerinscription(Request $request): Response
    {
//traiter les infos form
        $nom = $request->request->get('nom');
        $nom = trim($nom);

        $prenom = $request->request->get('prenom');
        $prenom = trim($prenom);

        $email = $request->request->get('email');
        $email = trim($email);

        $mdp = $request->request->get('mdp');
        
        
        $erreurs = [];
            if(empty($nom)){
                $erreurs['erreurNom'] = 'Nom Requis';
            }else if(!$this->nomPrenomValid($nom)){ //si le nom na aucun truc c'est ok
                $erreurs['erreurNom'] = 'Nom Invalide';
            }
            if(empty($prenom)){
                $erreurs['erreurPrenom'] = 'Prenom Requis';
            }else if(!$this->nomPrenomValid($prenom)){ //si le nom na aucun truc c'est ok
                $erreurs['erreurPrenom'] = 'Prenom Invalide';
            }
            if(empty($email)){
                $erreurs['erreurMail'] = 'Adresse email Requise';
            }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ //si le nom na aucun truc c'est ok
                $erreurs['erreurMail'] = 'Adresse email Invalide';
            }else {
                $testMail=$this->clientDAO->trouverParEmail($email);
                if($testMail){
                    $erreurs['erreurMail']= 'Adresse email déja utilisé';
                }
            }
            if(empty($mdp)){
                        $erreurs['erreurMdp'] = 'Mot de passe obligatoire';
                    } else if(mb_strlen($mdp) < 8 || !preg_match('/[0-9]/', $mdp)){
                        $erreurs['erreurMdp'] = 'Au moins 8 caratères dont au moins 1 chiffre';
                    }


            if($erreurs){
                    $erreurs['nom'] =$nom;
                    $erreurs['prenom'] =$prenom;
                    $erreurs['email'] =$email;
                        return $this->render('client/inscription-form.html.twig', $erreurs);
                    } else {
                        //TODO enregistrer le nouveau client dans la BDD

                        $client = new Client();
                        $client
                        ->setCliNom($nom)
                        ->setCliPrenom($prenom)
                        ->setCliEmail($email);
                        //hasher le mdp avant d'enregistré le client 
                        $mdp=$this->pwdEncoder->hashPassword($client, $mdp);
                        $client->setCliMdp($mdp);

                        //sauvegarder dans la BDD   
                        $this->clientDAO->sauvegarder($client);

                        $message = "Merci pour votre inscription " . strtoupper($nom) . " " . $prenom;
                        return $this->render('home/confirmation.html.twig', compact('message'));
                    }
    }

    private function nomPrenomValid($input) : bool
    {       
        $noAccent = strtr(utf8_decode($input), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
        return preg_match("/^[a-zA-Z]([- ',.a-zA-Z]{0,48}[.a-zA-Z])?$/", $noAccent);
    }
    

}
