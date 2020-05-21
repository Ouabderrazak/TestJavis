<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $Todaydate =  date("Ymd");   
       
        $u1 = new User();
        $u1->setFirstname("Marc")
            ->setLastname("Paris")
            ->setCreationdate(new \DateTime($Todaydate))
            ->setUpdatedate(new \DateTime($Todaydate));

        $manager->persist($u1);

        $u2 = new User();
        $u2->setFirstname("Fanny")
            ->setLastname("Barcelone")
            ->setCreationdate(new \DateTime($Todaydate))
            ->setUpdatedate(new \DateTime($Todaydate));

        $manager->persist($u2);

        $u3 = new User();
        $u3->setFirstname("Pierre")
            ->setLastname("Roma")
            ->setCreationdate(new \DateTime($Todaydate))
            ->setUpdatedate(new \DateTime($Todaydate));

        $manager->persist($u3);

        $manager->flush();
        
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
