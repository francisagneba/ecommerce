<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        for ($c = 0; $c < 6; $c++) {
            $category = new Category();
            $categoryName = ucfirst(implode(' ', $faker->words(2))); // Génère un nom de categorie en combinant 3 mots
            $category->setName($categoryName)
                ->setSlug(strtolower($this->slugger->slug($categoryName)));

            $manager->persist($category);

            for ($p = 0; $p < mt_rand(15, 20); $p++) {
                $product = new Product();
                $productName = ucfirst(implode(' ', $faker->words(3))); // Génère un nom de produit en combinant 3 mots
                $product->setName($productName)
                    ->setPrice($faker->price(4000, 20000))
                    ->setSlug($this->slugger->slug($productName))
                    ->setCategory($category)
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture($faker->imageUrl(400, 400, true));

                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}
