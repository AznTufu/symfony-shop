<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Event\ProductCreatedEvent;
use App\Event\ProductDeletedEvent;
use App\Event\ProductUpdatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProductCrudController extends AbstractCrudController
{
    private Security $security;
    private EventDispatcherInterface $dispatcher;
    
    public function __construct(Security $security, EventDispatcherInterface $dispatcher)
    {
        $this->security = $security;
        $this->dispatcher = $dispatcher;
    }
    
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm()->hideOnIndex(),
            TextField::new('name'),
            NumberField::new('price')
                ->setNumDecimals(2)
                ->setStoredAsString(false)
                ->setFormTypeOption('attr', [
                    'inputmode' => 'numeric',
                    'step' => '1',
                    'onkeydown' => 'return (event.keyCode !== 189 && event.keyCode !== 190 && event.keyCode !== 110)',
                    'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')"
                ])
                ->setFormTypeOption('html5', true)
                ->formatValue(function ($value) {
                    return $value . ' €';
                }),
            TextField::new('category'),
            TextEditorField::new('description'),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Product) {
            $user = $this->security->getUser();

            if ($user) {
                $entityInstance->setUser($user);
            }
        }

        parent::persistEntity($entityManager, $entityInstance);

        if ($entityInstance instanceof Product) {
            $this->dispatcher->dispatch(new ProductCreatedEvent($entityInstance));
        }
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::updateEntity($entityManager, $entityInstance);
        
        if ($entityInstance instanceof Product) {
            $this->dispatcher->dispatch(new ProductUpdatedEvent($entityInstance));
        }
    }
    
    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Product) {
            $this->dispatcher->dispatch(new ProductDeletedEvent($entityInstance));
        }
        
        parent::deleteEntity($entityManager, $entityInstance);
    }
}