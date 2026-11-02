<?php

namespace App\DataFixtures;

use App\Entity\Address\Barangay;
use App\Entity\Address\City;
use App\Entity\Address\Province;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AddressFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $province = new Province('Metro Manila', 'Metro Manila', null);
        $manager->persist($province);
        $manager->flush();

        $city = new City($province->getId(), 'Quezon City', 'Quezon City', null);
        $manager->persist($city);
        $manager->flush();

        $barangay = new Barangay($city->getId(), 'Diliman', 'Diliman', null);
        $manager->persist($barangay);
        $manager->flush();
    }
}
