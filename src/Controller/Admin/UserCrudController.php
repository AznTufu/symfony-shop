<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Event\UserUpdatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UserCrudController extends AbstractCrudController
{
    private EventDispatcherInterface $dispatcher;
    
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('edit',  fn (User $user) => sprintf('Update <strong>%s</strong>', $user->getEmail()));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            TextField::new('lastname')->hideOnForm(),
            TextField::new('firstname')->hideOnForm(),
            TextField::new('email')->hideOnForm(),
            ArrayField::new('roles')->hideOnForm(),
            NumberField::new('points')->hideOnForm(),
            BooleanField::new('active', 'Actif')
                ->renderAsSwitch(true)
        ];
    }
    
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);
        
        if ($entityInstance instanceof User) {
            $this->dispatcher->dispatch(new UserUpdatedEvent($entityInstance));
        }
    }
}