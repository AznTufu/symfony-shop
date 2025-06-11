<?php

namespace App\Controller\Admin;

use App\Entity\Notification;
use App\Entity\Product;
use App\Entity\User;
use App\Message\AddPointsToUsersMessage;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    #[Route('/admin/add-points', name: 'admin_add_points')]
    #[IsGranted('ROLE_ADMIN')]
    public function addPointsToUsers(): Response
    {
        // Envoyer un message pour traiter l'ajout de points de manière asynchrone
        $this->messageBus->dispatch(new AddPointsToUsersMessage(1000));
        
        // Ajouter un message flash pour informer l'utilisateur
        $this->addFlash('success', 'L\'ajout de 1000 points à tous les utilisateurs actifs est en cours de traitement.');
        
        // Rediriger vers la page d'accueil du dashboard
        return $this->redirectToRoute('admin');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Symfony Shop');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('User', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Product', 'fa fa-laptop', Product::class);
        yield MenuItem::linkToCrud('Notifications', 'fa fa-bell', Notification::class);

        yield MenuItem::linkToRoute('Ajouter 1000 points', 'fa fa-plus-circle', 'admin_add_points');
    }
}