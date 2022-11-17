<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAMS = [
        [
            "title" => "Pinocchio",
            "synopsis" => "La célèbre histoire de ce pantin de bois, Pinocchio, bien décidé à vivre la plus palpitante "
                          . "des aventures pour devenir un vrai petit garçon.",
            "poster" => "https://www.themoviedb.org/t/p/w600_and_h900_bestv2/nch8NTH45TBH4JyPuugttPzoxau.jpg",
            "category" => "category_Fantastique"
        ],
        [
            "title" => "Rick et Morty",
            "synopsis" => "Rick est un scientifique âgé et déséquilibré qui a récemment renoué avec sa famille. Il "
                          . "passe le plus clair de son temps à entraîner son petit-fils Morty dans des aventures "
                          . "extraordinaires et dangereuses, à travers l'espace et dans des univers parallèles. "
                          . "Ajoutés à la vie de famille déjà instable de Morty, ces événements n'amènent qu'un "
                          . "surcroît de stress pour Morty, à la maison et à l'école...",
            "poster" => "https://www.themoviedb.org/t/p/w600_and_h900_bestv2/s11re4xQLZ6pRPv2sqXnK8CCvGn.jpg",
            "category" => "category_Animation"
        ],
        [
            "title" => "Sonic 2",
            "synopsis" => "Bien installé dans la petite ville de Green Hills, Sonic veut maintenant prouver qu’il a "
                          . "l’étoffe d' un véritable héros. Un défi de taille se présente à lui quand le Dr Robotnik "
                          . "refait son apparition. Accompagné de son nouveau complice Knuckles, ils sont en quête "
                          . "d’une émeraude dont le pouvoir permettrait de détruire l’humanité tout entière. Pour "
                          . "s’assurer que l’émeraude ne tombe entre de mauvaises mains, Sonic fait équipe avec Tails. "
                          . "Commence alors un voyage à travers le monde, plein de péripéties.",
            "poster" => "https://www.themoviedb.org/t/p/w600_and_h900_bestv2/7RSCL6V8BlekgVnNPok6tLW50tP.jpg",
            "category" => "category_Aventure"
        ],
        [
            "title" => "The Walking Dead",
            "synopsis" => "Après une apocalypse, ayant transformé la quasi-totalité de la population en zombies, un "
                          . "groupe d'hommes et de femmes, mené par le shérif adjoint Rick Grimes, tente de "
                          . "survivre... Ensemble, ils vont devoir, tant bien que mal, faire face à ce nouveau monde, "
                          . "devenu méconnaissable, à travers leur périple dans le Sud profond des États-Unis.",
            "poster" => "https://www.themoviedb.org/t/p/w600_and_h900_bestv2/4UZzJ65UxR6AsKL6zjFWNYAKb3w.jpg",
            "category" => "category_Horreur"
        ],
        [
            "title" => "The Takeover",
            "synopsis" => "Ayant découvert un virus dangereux pour la vie privée des gens, une hackeuse éthique se "
                          . "retrouve au cœur d'une terrible histoire de corruption. Quand elle apparaît en train de "
                          . "commettre un meurtre dans une vidéo truquée, elle doit échapper à la police et dépister "
                          . "les criminels qui la font chanter.",
            "poster" => "https://www.themoviedb.org/t/p/w600_and_h900_bestv2/g7rdcofib7HqdlDP1LT7Hmf1f2o.jpg",
            "category" => "category_Action"
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::PROGRAMS as $index => $programData) {
            $program = new Program();
            $program->setTitle($programData["title"]);
            $program->setSynopsis($programData["synopsis"]);
            $program->setPoster($programData["poster"]);
            $program->setCategory($this->getReference($programData["category"]));
            $this->addReference('program_' . $index, $program);

            $manager->persist($program);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
