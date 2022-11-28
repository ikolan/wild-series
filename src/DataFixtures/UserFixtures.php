<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $lambda = new User();
        $lambda->setUsername('Lambda');
        $lambda->setBio("Lambda");
        $lambda->setEmail('lambda@wildseries.com');
        $lambda->setRoles(['ROLE_USER']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $lambda,
            'lambdapassword'
        );

        $lambda->setPassword($hashedPassword);
        $manager->persist($lambda);

        $admin = new User();
        $admin->setUsername("Admin");
        $admin->setBio("Admin");
        $admin->setEmail('admin@wildseries.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword(
            $admin,
            'adminpassword'
        );
        $admin->setPassword($hashedPassword);
        $manager->persist($admin);

        $manager->flush();
    }
}
