<?php

namespace App\Controller;

use App\Service\UserService;
use App\Service\TaskService;
use App\Dto\TaskFormDto;
use App\Form\TaskForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/users', name: 'admin_users')]
    public function usersList(UserService $userService): Response
    {
        $users = $userService->getAllUsers();

        return $this->render('admin/users_list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/users/{userId}/tasks', name: 'admin_user_tasks')]
    public function userTasks(int $userId, UserService $userService, TaskService $taskService): Response
    {
        $user = $userService->getUserById($userId);
        if (!$user) {
            throw $this->createNotFoundException('Пользователь не найден');
        }

        $tasks = $taskService->getAllTasksForUser($user);

        return $this->render('admin/user_tasks.html.twig', [
            'user' => $user,
            'tasks' => $tasks,
        ]);
    }

    #[Route('/users/{userId}/tasks/{taskId}/edit', name: 'admin_task_edit')]
    public function editUserTask(
        Request $request,
        int $userId,
        int $taskId,
        UserService $userService,
        TaskService $taskService
    ): Response {
        $user = $userService->getUserById($userId);
        if (!$user) {
            throw $this->createNotFoundException('Пользователь не найден');
        }

        $task = $taskService->getTaskById($taskId);
        if (!$task || $task->getOwner() !== $user) {
            throw $this->createNotFoundException('Задача не найдена или не принадлежит пользователю');
        }

        $dto = new TaskFormDto();
        $dto->name = $task->getName();
        $dto->description = $task->getDescription();
        $dto->deadline = $task->getDeadline();

        $form = $this->createForm(TaskForm::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskService->updateTask($task, $dto);
            return $this->redirectToRoute('admin_user_tasks', ['userId' => $userId]);
        }

        return $this->render('admin/task_edit.html.twig', [
            'form' => $form,
            'task' => $task,
            'user' => $user,
        ]);
    }

    #[Route('/users/{userId}/tasks/{taskId}/change-status', name: 'admin_task_change_status', methods: ['POST'])]
    public function changeUserTaskStatus(
        int $userId,
        int $taskId,
        UserService $userService,
        TaskService $taskService
    ): Response {
        $user = $userService->getUserById($userId);
        if (!$user) {
            throw $this->createNotFoundException('Пользователь не найден');
        }

        $task = $taskService->getTaskById($taskId);
        if (!$task || $task->getOwner() !== $user) {
            throw $this->createNotFoundException('Задача не найдена или не принадлежит пользователю');
        }

        $taskService->changeTaskStatus($task);
        return $this->redirectToRoute('admin_user_tasks', ['userId' => $userId]);
    }
}
