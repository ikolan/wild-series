<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public const SEASONS_PER_PROGRAM = 5;

    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create();

        for ($programIndex = 0; $programIndex < count(ProgramFixtures::PROGRAMS); $programIndex++) {
            for ($seasonIndex = 0; $seasonIndex < self::SEASONS_PER_PROGRAM; $seasonIndex++) {
                $season = new Season();
                $season->setNumber($seasonIndex + 1);
                $season->setYear($faker->year());
                $season->setDescription($faker->paragraphs(3, true));
                $season->setProgram($this->getReference('program_' . ProgramFixtures::PROGRAMS[$programIndex]["slug"]));
                $this->addReference(
                    'season_' . ($programIndex * self::SEASONS_PER_PROGRAM) + $seasonIndex,
                    $season
                );

                $manager->persist($season);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
           ProgramFixtures::class,
        ];
    }
}
