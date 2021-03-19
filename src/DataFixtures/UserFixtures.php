<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    /**
     *
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     *
     * @var $faker
     */
    private $faker;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create();
    }
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail("mohammedbentiress@sqli.com");
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            'the_new_password'
        ));
        $manager->persist($user);

        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['group2'];
    }
}
