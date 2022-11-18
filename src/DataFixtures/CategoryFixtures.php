<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    private const CATEGORIES = [
        ['name' => 'Action', 'slug' => 'action'],
        ['name' => 'Aventure', 'slug' => 'aventure'],
        ['name' => 'Animation', 'slug' => 'animation'],
        ['name' => 'Fantastique', 'slug' => 'fantastique'],
        ['name' => 'Horreur', 'slug' => 'horreur']
    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::CATEGORIES as $categoryData) {
            $category = new Category();
            $category->setName($categoryData["name"]);
            $category->setSlug($categoryData["slug"]);
            $manager->persist($category);
            $this->addReference('category_' . $categoryData["slug"], $category);
        }
        $manager->flush();
    }
}
