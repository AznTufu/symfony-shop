<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/products')]
final class ProductController extends AbstractController
{
    #[Route('/', name: 'product_index')]
    #[IsGranted('ROLE_USER')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/{id}', name: 'product_detail', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function detail(Product $product): Response
    {
        return $this->render('product/detail.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/buy', name: 'product_buy', methods: ['POST'], requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function buy(Product $product, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        
        // Vérifier si l'utilisateur est actif - correction de la méthode
        if (!$user->isActive()) {
            $this->addFlash('danger', 'Votre compte est désactivé. Vous ne pouvez pas effectuer d\'achats.');
            return $this->redirectToRoute('product_detail', ['id' => $product->getId()]);
        }
        
        $userPoints = $user->getPoints() ?? 0;
        $productPrice = $product->getPrice();

        if ($userPoints < $productPrice) {
            $this->addFlash('danger', 'Vous n\'avez pas assez de points pour acheter ce produit.');
            return $this->redirectToRoute('product_detail', ['id' => $product->getId()]);
        }

        // Déduire les points
        $user->setPoints($userPoints - $productPrice);

        // Créer une notification pour l'achat
        $notification = new Notification();
        $notification->setUser($user);
        $notification->setLabel(sprintf(
            'Vous avez acheté le produit "%s" pour %d points.',
            $product->getName(),
            $productPrice
        ));

        $entityManager->persist($notification);
        $entityManager->flush();

        $this->addFlash('success', sprintf(
            'Félicitations ! Vous avez acheté "%s" pour %d points. Il vous reste %d points.',
            $product->getName(),
            $productPrice,
            $user->getPoints()
        ));

        return $this->redirectToRoute('product_index');
    }
}