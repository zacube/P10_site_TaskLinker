<?php
namespace App\Factory;

use App\Entity\Project;
use Faker\Factory as FakerFactory;

class ProjectFactory
{
    public static function create(): Project
    {
        $faker = FakerFactory::create();

        $project = new Project();
        $project->setName($faker->sentence(3));
        $project->setStatus($faker->boolean(80)); // 80% de chance d'être "actif"

        return $project;
    }

    public static function createMany(int $count): array
    {
        $projects = [];
        for ($i = 0; $i < $count; $i++) {
            $projects[] = self::create();
        }
        return $projects;
    }
}
