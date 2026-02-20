<?php

namespace App\Controller;

use App\Entity\Project;
use App\Enum\TaskStatus;
use App\Enum\EmployeeRole;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Security\Voter\ProjectVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(EmployeeRole::User->value)]
final class ProjectController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/project', name: 'app_project_index')]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findByUser($this->getUser()),
        ]);
    }

    #[Route('/project/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        if (!$this->isGranted(EmployeeRole::Manager->value)) {
            $this->addFlash('danger', "Vous n'avez pas accès à cette page.");
            return $this->redirectToRoute('app_project_index');
        }

        $project = (new Project())
            ->setStatus(true); // on impose un statut au projet courant (false = archivé)

        $form = $this
            ->createForm(ProjectType::class, $project)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($project);
            $manager->flush();

            return $this->redirectToRoute('app_project_detail', ['id' => $project->getId()]);
        }

        return $this->render('project/new.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
        ]);
    }

    #[Route('/project/{id}', name: 'app_project_detail', requirements: ['id' => '\d+'])]
    public function detail(?Project $project): Response
    {
        // Projet introuvable ou pas autorisé → redirection
        if ($project === null || !$this->isGranted(ProjectVoter::VIEW, $project)) {
            $this->addFlash('danger', "Vous n'avez pas accès à ce projet.");
            return $this->redirectToRoute('app_project_index');
        }

        $tasks = $project->getTasks();
        $todo = [];
        $doing = [];
        $done = [];
        foreach ($tasks as $task) {
            match ($task->getStatus()) {
                TaskStatus::ToDo => $todo[] = ['task' => $task],
                TaskStatus::Doing => $doing[] = ['task' => $task],
                TaskStatus::Done => $done[] = ['task' => $task],
                default => null,
            };
        }

        return $this->render('project/detail.html.twig', [
            'project' => $project,
            'todo' => $todo,
            'doing' => $doing,
            'done' => $done
        ]);
    }

    #[Route('/project/{id}/edit', name: 'app_project_edit')]
    public function edit(?Project $project, Request $request, EntityManagerInterface $manager): Response
    {
        // Projet introuvable ou pas autorisé → redirection
        if ($project === null || !$this->isGranted(ProjectVoter::VIEW, $project)) {
            $this->addFlash('danger', "Vous n'avez pas accès à ce projet, ou il n'existe pas.");
            return $this->redirectToRoute('app_project_index');
        }

        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush(); // inutile de persister : $project existe déjà
            $this->addFlash('success', 'Projet modifié avec succès.');

            return $this->redirectToRoute('app_project_index');
        }

        return $this->render('project/new.html.twig', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }

    #[Route('/project/{id}/confirm-archive', name: 'app_project_confirm_archive')]
    #[IsGranted(EmployeeRole::Manager->value)]
    public function confirmArchive(Project $project): Response
    {
        return $this->render('project/confirm_archive.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/project/{id}/archive', name: 'app_project_archive', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function archive(Project $project, EntityManagerInterface $manager): Response
    {
        // Projet introuvable → redirection
        if ($project === null || !$this->isGranted(ProjectVoter::VIEW, $project)) {
            $this->addFlash('danger', "Vous n'avez pas accès à ce projet, ou il n'existe pas.");
            return $this->redirectToRoute('app_project_index');
        }

        $project->setStatus(false);
        $manager->flush();
        $this->addFlash('success', 'Projet archivé !');

        return $this->redirectToRoute('app_project_index');
    }
}
