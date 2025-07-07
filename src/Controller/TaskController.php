<?php

namespace App\Controller;

use App\Dto\TaskFormDto;
use App\Form\TaskForm;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Dto\TaskFilterDto;
use App\Form\TaskFilterForm;

class TaskController extends AbstractController
{
    #[Route('/tasks/create', name: 'task_create')]
    public function create(Request $request, TaskService $taskService): Response
    {
        $dto = new TaskFormDto();
        $form = $this->createForm(TaskForm::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $taskService->createTask($dto, $user);

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/tasks', name: 'task_list')]
    public function list(Request $request, TaskService $taskService): Response
    {
        $user = $this->getUser();

        $filter = new TaskFilterDto();
        $form = $this->createForm(TaskFilterForm::class, $filter);
        $form->handleRequest($request);

        $tasks = $taskService->getFilteredTasksForUser($user, $filter);

        return $this->render('task/list.html.twig', [
            'filterForm' => $form,
            'tasks' => $tasks,
        ]);
    }



    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function edit(Request $request, TaskService $taskService, int $id): Response
    {
        $task = $taskService->getTaskById($id);

        if (!$task || $task->getOwner() !== $this->getUser()) {
            throw $this->createNotFoundException('Задача не найдена или доступ запрещен');
        }

        $dto = new TaskFormDto();
        $dto->name = $task->getName();
        $dto->description = $task->getDescription();
        $dto->deadline = $task->getDeadline();

        $form = $this->createForm(TaskForm::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskService->updateTask($task, $dto);
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form,
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/change-status', name: 'task_change_status', methods: ['POST'])]
    public function changeStatus(Request $request, TaskService $taskService, int $id): Response
    {
        $task = $taskService->getTaskById($id);

        if (!$task || $task->getOwner() !== $this->getUser()) {
            throw $this->createNotFoundException('Задача не найдена или доступ запрещен');
        }

        $taskService->changeTaskStatus($task);

        return $this->redirectToRoute('task_list');
    }
}
