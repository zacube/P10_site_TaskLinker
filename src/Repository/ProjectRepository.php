<?php

namespace App\Repository;

use App\Entity\Employee;
use App\Entity\Project;
use App\Enum\EmployeeRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findByUser(?Employee $user): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', true);

        // Si l'utilisateur existe et n'est pas manager
        if ($user && !in_array(EmployeeRole::Manager->value, $user->getRoles())) {
            $qb->innerJoin('p.team', 'e')
                ->andWhere('e.id = :userId')
                ->setParameter('userId', $user->getId());
        }

        return $qb->getQuery()
            ->getResult();
    }
}
