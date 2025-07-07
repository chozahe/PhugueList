<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Dto\TaskFilterDto;
use App\Enum\TaskStatus;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findFilteredTasksForUser(User $user, TaskFilterDto $filter): array
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.owner = :user')
            ->setParameter('user', $user);

        if ($filter->status !== null) {
            $qb->andWhere('t.status = :status')
                ->setParameter('status', $filter->status);
        }

        if ($filter->fromDate !== null) {
            $qb->andWhere('t.deadline >= :fromDate')
                ->setParameter('fromDate', $filter->fromDate);
        }

        if ($filter->toDate !== null) {
            $qb->andWhere('t.deadline <= :toDate')
                ->setParameter('toDate', $filter->toDate);
        }

        if ($filter->onlyOverdue) {
            $now = new \DateTimeImmutable();
            $qb->andWhere('t.deadline < :now')
                ->andWhere('t.status != :completed')
                ->setParameter('now', $now)
                ->setParameter('completed', TaskStatus::COMPLETED);
        }

        return $qb
            ->orderBy('t.deadline', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
