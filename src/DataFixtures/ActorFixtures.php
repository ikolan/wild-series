<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\String\Slugger\SluggerInterface;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();
        $actorsCount = 10;

        for ($actorIndex = 0; $actorIndex < $actorsCount; $actorIndex++) {
            $programCount = 3;
            $programs = ProgramFixtures::PROGRAMS;
            shuffle($programs);

            $actor = new Actor();
            $actor->setFirstName($faker->firstName());
            $actor->setLastName($faker->lastName());
            $actor->setBirthDate(new \DateTimeImmutable($faker->date()));
            $actor->setSlug($this->slugger->slug($actor->getFullName()));
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
