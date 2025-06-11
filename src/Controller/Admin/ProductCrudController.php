<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Core\Security;

class ProductCrudController extends AbstractCrudController
{
    private Security $security;
    
    public function __construct(Security $security)
    {
        $this->security = $security;
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
    }
}