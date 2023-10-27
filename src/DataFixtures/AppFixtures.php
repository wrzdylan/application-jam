<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\User;
use App\Entity\Order;
use App\Entity\LineOrder;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;


class AppFixtures extends Fixture
{
    public function __construct (UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }
    public function load(ObjectManager $manager): void
    {

        $categories = [
            "intense",
            "confiture",
            "gelée",
            "fruits rouges"
        ];
        $products=[];
        $products["Cerises"] = [
            "3.90",
            [1, 3]
        ] ;
        $products["Myrtille"] = [
            "3.90",
            [1, 3]
        ] ;
        $products["Prune"] = [
            "3.10",
            [1]
        ] ;
        $products["Figue"] = [
            "3.30",
            [1]
        ] ;
        $products["Fraise"] = [
            "3.70",
            [1, 3]
        ] ;
        $products["Framboise"] = [
            "4.20",
            [1, 3]
        ] ;
        $products["Gelée Cassis"] = [
            "3.70",
            [3, 2]
        ] ;
        $products["Gelée Coings"] = [
            "3.70",
            [2]
        ] ;
        $products["Intense Abricot"] = [
            "4.70",
            [0, 1]
        ] ;
        $products["Intense Fraise"] = [
            "4.90",
            [0, 1, 3]
        ] ;
        $products["Intense Fruits Rouges"] = [
            "4.80",
            [0, 1, 3]
        ] ;
        $products["Intense Myrtille"] = [
            "5.90",
            [0, 1, 3]
        ];


        $slugger = new AsciiSlugger();


        $categoriesInstance = [];

        foreach ($categories as $value) {
            $category = new Category();
            $category->setName($value);
            $category->setSlug($slugger->slug($value));
            $manager->persist($category);
            $categoriesInstance[] = $category;
        }

        $productsInstance = [];

        foreach ($products as $key => $product) {
            $productInstance = new Product();
            $productInstance->setName($key);
            $productInstance->setPrice(floatval($product[0])*100);
            foreach ($product[1] as $category) {
                $productInstance->addCategory($categoriesInstance[$category]);
            }

            $image = $slugger->slug($productInstance->getName());
            $productInstance->setImage(strtolower($image) . '.jpeg');

            $productsInstance[$key] = $productInstance;

            $manager->persist($productInstance);
        }


        for ($i = 1; $i <= 4; $i++) {
            $user = new User();
            $user->setEmail("user_{$i}@user.com");
            $user->setPassword(
                $this->userPasswordHasherInterface->hashPassword(
                    $user, "ilove_user{$i}jam"
                )
            );
            $user->setRoles(["ROLE_USER"]);

            $order = new Order();
            $order->setOwner($user);

            $product = $productInstance[rand(0, 5)];

            $lineOrder = new LineOrder();
            $lineOrder->setProduct($product);
            $lineOrder->setQuantity(rand(1, 8));
            $lineOrder->setSubtotal($lineOrder->getQuantity() * $product->getPrice());
            $lineOrder->setOrderAssociated($order);

            $manager->persist($lineOrder);
            $manager->persist($user);
            $manager->persist($order);
        }

        $user = new User();
        $user->setEmail("admin@admin.com");
        $user->setPassword(
            $this->userPasswordHasherInterface->hashPassword(
                $user, "ilovejam"
            )
        );
        $user->setRoles(["ROLE_ADMIN"]);

        $user_2 = new User();
        $user_2->setEmail("owner@owner.com");
        $user_2->setPassword(
            $this->userPasswordHasherInterface->hashPassword(
                $user, "iloveownerjam"
            )
        );
        $user_2->setRoles(["ROLE_OWNER"]);

        $manager->persist($user);
        $manager->persist($user_2);

        $manager->flush();
    }
}