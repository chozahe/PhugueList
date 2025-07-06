<?php

namespace App\Controller;

use App\Dto\TaskFormDto;
use App\Form\TaskForm;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
}
