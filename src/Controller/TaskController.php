<?php

namespace App\Controller;


use App\Entity\Project;
use App\Entity\Task;
use App\Enum\TaskStatus;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    #[Route('project/{id}/task/new/{status?}', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Project $project, ?string $status = null, EntityManagerInterface $manager): Response
    {
        $task = new Task();
        $task->setProject($project); // on lie la tâche au projet courant
        if ($status) {
            // le statut est défini selon la valeur reçue via l'url,
            $task->setStatus(TaskStatus::from($status));
        }

        $form = $this->createForm(TaskType::class, $task, [
            'team' => $project->getTeam(), // Passe l'équipe du projet
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($task);
            $manager->flush();

            return $this->redirectToRoute('app_project_detail', ['id' => $project->getId()]);
        }

        return $this->render('task/new.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('task/{id}/edit', name: 'app_task_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Task $task, Request $request, EntityManagerInterface $manager): Response
    {
        $project = $task->getProject();
        $form = $this->createForm(TaskType::class, $task, [
            'team' => $project->getTeam(), // Passe l'équipe du projet
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($task);
            $manager->flush();

            return $this->redirectToRoute('app_project_detail', ['id' => $project->getId()]);
        }

        return $this->render('task/new.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('task/{id}/delete', name: 'app_task_delete', requirements: ['id' => '\d+'])]
    public function delete(Task $task, EntityManagerInterface $manager): Response
    {
        $manager->remove($task);
        $manager->flush();
        $this->addFlash('success', 'Tâche supprimée avec succès.');

        return $this->redirectToRoute('app_project_detail', [
            'id' => $task->getProject()->getId()
        ]);
    }
}
