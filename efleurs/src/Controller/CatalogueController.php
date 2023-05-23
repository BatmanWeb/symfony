<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatalogueController extends AbstractController
{

    private $articleDAO;

    public function __construct(ArticleRepository $articleDAO){
        $this->articleDAO = $articleDAO;
    }


    /**
     * @Route("/catalogue/article_details/{id}", name="article_details")
     */
    public function afficherArt($id): Response{
        $article = $this->articleDAO->findByIdAndActif($id, true);
        if($article){
            return $this->render('catalogue/article_details.html.twig', [
                'article' => $article,
             ]);
        }else {
            $message="ressource introuvable";
            return $this->render('home/erreur404.html.twig', compact('message'));
        }
    }
    /**
     * @Route("/catalogue", name="catalogue")
     */
    public function index(): Response
    {
        $articles = $this->articleDAO->findByActif(true);
        return $this->render('catalogue/articles.html.twig', [
           'articles' => $articles,
        ]);
    }
}
