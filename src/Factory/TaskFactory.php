<?php
namespace App\Factory;

use App\Entity\Task;
use App\Enum\TaskStatus;
use Faker\Factory as FakerFactory;

class TaskFactory
{
    public static function create(): Task
    {
        $faker = FakerFactory::create();

        $task = new Task();
        $task->setName($faker->sentence(4));
        $task->setDescription($faker->paragraph());
        $task->setDeadline($faker->dateTimeBetween('now', '+6 months'));
        $statuses = TaskStatus::cases();
        $task->setStatus($faker->randomElement($statuses));

        return $task;
    }

    public static function createMany(int $count): array
    {
        $tasks = [];
        for ($i = 0; $i < $count; $i++) {
            $tasks[] = self::create();
        }
        return $tasks;
    }
}
