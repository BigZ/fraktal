<?php

/*
 * This file is part of the promote-api package.
 *
 * (c) Bigz
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace App\DataFixtures\ORM;

use App\Entity\Artist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class LoadArtistData
 * @author Romain Richard
 */
class ArtistFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getArtists() as $data) {
            $artist = new Artist();
            $artist->setName($data['name']);
            $artist->setSlug($data['slug']);
            $artist->setBio($data['bio']);
            $artist->setCreatedAt(new \DateTime('now'));
            $artist->setUpdatedAt(new \DateTime('now'));

            foreach ($data['labels'] as $label) {
                $artist->addLabel($manager->getRepository('App:Label')->findOneBySlug($label));
            }

            $manager->persist($artist);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies()
    {
        return [LabelFixtures::class];
    }

    /**
     * @return array
     */
    private function getArtists()
    {
        return [
            [
                'name' => 'Bob Marley',
                'slug' => 'bob-marley',
                'bio' => 'Bob is a <b>reggae</b> legend',
                'createdBy' => 'user1',
                'labels' => ['island-records', 'tuff-gong'],
                'imageName' => 'bob-marley.jpg',
            ],
            [
                'name' => 'Peter Tosh',
                'slug' => 'peter-tosh',
                'bio' => 'Tosh is the bush doctor !',
                'createdBy' => 'user1',
                'labels' => ['tuff-gong'],
            ],
            [
                'name' => 'Daft Punk',
                'slug' => 'daftpunk',
                'bio' => 'The robot musicians',
                'createdBy' => 'user2',
                'labels' => ['ninja-tune'],
            ],
            [
                'name' => 'Maitre Gims',
                'slug' => 'maitregims',
                'bio' => 'Aka Gandhi Djuna de Kinshasa',
                'createdBy' => 'user3',
                'labels' => ['wati-b'],
            ],
        ];
    }
}
