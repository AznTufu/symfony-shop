<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AppController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        // Si l'utilisateur est connecté, rediriger vers la liste des produits
        if ($this->getUser()) {
            return $this->redirectToRoute('product_index');
        }
        
        // Sinon, afficher la page d'accueil pour les utilisateurs non connectés
        return $this->render('app/index.html.twig', [
            'controller_name' => 'AppController',
        ]);
    }
}