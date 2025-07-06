<?php

namespace App\Service;

use App\Dto\TaskFormDto;
use App\Entity\User;
use App\Enum\TaskStatus;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createTask(TaskFormDto $dto, User $owner): Task
    {
        $task = new Task();
        $task->setName($dto->name);
        $task->setDescription($dto->description);
        $task->setDeadline($dto->deadline);
        $task->setStatus(TaskStatus::NEW);
        $task->setCreatedAt(new \DateTimeImmutable());
        $task->setOwner($owner);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }
}
