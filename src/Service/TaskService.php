<?php

namespace App\Service;

use App\Dto\TaskFormDto;
use App\Entity\User;
use App\Enum\TaskStatus;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Dto\TaskFilterDto;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    private TaskRepository $taskRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, TaskRepository $taskRepository)
    {
        $this->entityManager = $entityManager;
        $this->taskRepository = $taskRepository;
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


    public function updateTask(Task $task, TaskFormDto $dto): Task
    {
        $task->setName($dto->name);
        $task->setDescription($dto->description);
        $task->setDeadline($dto->deadline);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }

    public function changeTaskStatus(Task $task): Task
    {
        $currentStatus = $task->getStatus();
        $nextStatus = match ($currentStatus) {
            TaskStatus::NEW => TaskStatus::IN_PROGRESS,
            TaskStatus::IN_PROGRESS => TaskStatus::COMPLETED,
            TaskStatus::COMPLETED => TaskStatus::COMPLETED,
        };

        $task->setStatus($nextStatus);
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }

    public function getFilteredTasksForUser(User $user, TaskFilterDto $filter): array
    {
        return $this->taskRepository->findFilteredTasksForUser($user, $filter);
    }

    public function getAllTasksForUser(User $user): array
    {
        return $this->taskRepository->findBy(['owner' => $user]);
    }

    public function getTaskById(int $id): ?Task
    {
        return $this->taskRepository->find($id);
    }
}
