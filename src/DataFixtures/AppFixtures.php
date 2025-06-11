<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@admin.fr');
        $admin->setRoles(['ROLE_ADMIN']);

        $plainPassword = 'admin';
        $hashedPassword = $this->passwordHasher->hashPassword($admin, $plainPassword);
        $admin->setPassword($hashedPassword);
        $admin->setLastname('Admin');
        $admin->setFirstname('Administrator');
        $admin->setPoints(1000);
        $admin->setActive(true);

        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@user.fr');
        $user->setRoles(['ROLE_USER']);
        $user->setLastname('User');
        $user->setFirstname('Normal');
        $user->setPoints(100);
        $user->setActive(true);

        $plainPassword = 'user';
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);
        
        $manager->persist($user);
        
        $manager->flush();
    }
}