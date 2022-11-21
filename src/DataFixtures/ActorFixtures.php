<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();
        $actorsCount = 10;

        for ($actorIndex = 0; $actorIndex < $actorsCount; $actorIndex++) {
            $programCount = 3;
            $programs = ProgramFixtures::PROGRAMS;
            shuffle($programs);

            $actor = new Actor();
            $actor->setName($faker->name);
            for ($programIndex = 0; $programIndex < $programCount; $programIndex++) {
                $actor->addProgram($this->getReference("program_" . $programs[$programIndex]["slug"]));
            }

            $manager->persist($actor);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProgramFixtures::class
        ];
    }
}
