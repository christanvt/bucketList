<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use App\Entity\Wish;
use DateTimeImmutable;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Length;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->loadUser(15);

        $this->loadCategorys();
        $this->loadWishs(10);

        $manager->flush();
    }
    public function loadUser(int $count): void
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < $count; $i++) {
            $user = new User();

            $username = $faker->username();
            $lastname = $faker->lastName();
            $mailDomain = $faker->freeEmailDomain();

            $email = strtolower($username) . '.' . strtolower($lastname) . '-' . $i . $mailDomain;
            $password = $this->encoder->hashPassword($user, '123');

            $newDate = new DateTimeImmutable('now');


            $user->setFirstname($username)
                ->setLastname($lastname)
                ->setEmail($email)
                ->setPassword($password);

            $this->manager->persist($user);
        }

        $this->manager->flush();
    }

    public function loadCategorys(): void
    {
        $itemsArray = [
            'Travel & Adventure',
            'Sport',
            'Entertainment',
            "Human Relations",
            'Others',
        ];
        for ($i = 0; $i < count($itemsArray); $i++) {
            $item = $itemsArray[$i];
            $category = new Category();
            $category->setName($item);
            $this->manager->persist($category);
        }
        $this->manager->flush();
    }

    public function loadWishs(int $count): void
    {
        $faker = Faker\Factory::create('fr_FR');


        for ($i = 0; $i < $count; $i++) {
            $categoryLength = count($this->manager->getRepository(Category::class)->findAll()) - 1;
            $category = $this->manager->getRepository(Category::class)->findAll()[random_int(0, $categoryLength)];
            $newDate = $faker->dateTime($max = 'now', $timezone = null);
            $title = $faker->word();
            $description = $faker->text($maxNbChars = 100);
            $userLength = count($this->manager->getRepository(User::class)->findAll()) - 1;
            $author = $this->manager->getRepository(User::class)->findAll()[random_int(0, $userLength)];

            $wish = new Wish();

            $wish->setTitle($title)
                ->setDescription($description)
                ->setAuthor($author->getFirstname())
                ->setIsPublished(true)
                ->setDateCreated($newDate)
                ->setCategory($category);

            $this->manager->persist($wish);
        }
        $this->manager->flush();
    }
}
