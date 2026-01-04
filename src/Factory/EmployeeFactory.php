<?php
namespace App\Factory;

use App\Entity\Employee;
use App\Enum\EmployeeStatus;
use Faker\Factory as FakerFactory;

class EmployeeFactory
{
    public static function create(): Employee
    {
        $faker = FakerFactory::create();

        $employee = new Employee();
        $employee->setFirstname($faker->firstName());
        $employee->setName($faker->lastName());
        $employee->setEmail($faker->unique()->safeEmail());
        $employee->setPassword($faker->password);
        $employee->setEntryDate(\DateTimeImmutable::createFromMutable($faker->dateTimeThisDecade()));
        $statuses = EmployeeStatus::cases();
        $employee->setStatus($faker->randomElement($statuses));

        return $employee;
    }

    public static function createMany(int $count): array
    {
        $employees = [];
        for ($i = 0; $i < $count; $i++) {
            $employees[] = self::create();
        }
        return $employees;
    }
}
