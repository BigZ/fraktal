<?php

namespace App\DataFixtures\ORM;

use App\Entity\Label;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadLabelData
 * @author Romain Richard
 */
class LabelFixtures extends Fixture
{
    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getLabels() as $data) {
            $label = new Label();
            $label->setName($data['name']);
            $label->setSlug($data['slug']);
            $label->setDescription($data['description']);
            $label->setCreatedAt(new \DateTime('now'));
            $label->setUpdatedAt(new \DateTime('now'));

            $manager->persist($label);
        }

        $manager->flush();
    }

    /**
     * @return array
     */
    private function getLabels()
    {
        return [
            [
                'name' => 'Island Records',
                'slug' => 'island-records',
                'description' => 'Music from the tropics',
            ],
            [
                'name' => 'Tuff Gong',
                'slug' => 'tuff-gong',
                'description' => 'Music from the ghetto',
            ],
            [
                'name' => 'Ninja Tune',
                'slug' => 'ninja-tune',
                'description' => 'Black hooded sounds',
            ],
            [
                'name' => 'Wati B',
                'slug' => 'wati-b',
                'description' => 'Le label du rap francais',
            ],
        ];
    }
}
