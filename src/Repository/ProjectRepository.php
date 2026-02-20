<?php

namespace App\Repository;

use App\Entity\Employee;
use App\Entity\Project;
use App\Enum\EmployeeRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private Security $security)
    {
        parent::__construct($registry, Project::class);
    }

    public function findByUser(?Employee $user): array
    {
        // Manager : tous les projets non archivés
        if ($this->security->isGranted(EmployeeRole::Manager->value)) {
            return $this->findBy(['status' => true]);
        }

        // User : uniquement ses projets non archivés
        return $this->createQueryBuilder('p')
            ->innerJoin('p.team', 'e')
            ->andWhere('e = :employee')
            ->andWhere('p.status = :status')
            ->setParameter('employee', $user)
            ->setParameter('status', true)
            ->getQuery()
            ->getResult();
    }
}
