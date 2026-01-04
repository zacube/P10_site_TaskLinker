<?php
namespace App\DataFixtures;

use App\Factory\EmployeeFactory;
use App\Factory\ProjectFactory;
use App\Factory\TaskFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // --- Crée des projets ---
        $projects = ProjectFactory::createMany(5);

        // --- Crée des employés ---
        $employees = EmployeeFactory::createMany(10);

        // --- Associe chaque employé à 1 à 3 projets ---
        foreach ($employees as $employee) {
            $assignedProjects = (array)array_rand($projects, rand(1, 3));

            foreach ($assignedProjects as $index) {
                $project = $projects[$index];
                $employee->getProjects()->add($project);
                $project->getTeam()->add($employee);
            }
            $manager->persist($employee);
        }

        // --- Crée des tâches pour chaque projet ---
        foreach ($projects as $project) {
            $numTasks = rand(2, 5);

            for ($i = 0; $i < $numTasks; $i++) {
                $task = TaskFactory::create();
                $task->setProject($project);

                // Choisit un employé du projet comme "holder"
                $team = $project->getTeam()->toArray();
                if (!empty($team)) {
                    $task->setHolder($team[array_rand($team)]);
                }

                $project->getTasks()->add($task);
                $manager->persist($task);
            }

            $manager->persist($project);
        }

        $manager->flush();
    }
}
