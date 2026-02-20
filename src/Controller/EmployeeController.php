<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Enum\EmployeeRole;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use App\Security\Voter\ProjectVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/employee', name: 'app_employee_')]
#[IsGranted(EmployeeRole::User->value)]
final class EmployeeController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(EmployeeRepository $employeeRepository): Response
    {

        $employees = $employeeRepository->findAll();

        return $this->render('employee/index.html.twig', [
            'employees' => $employees
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(?Employee $employee, Request $request, EntityManagerInterface $manager): Response
    {
        if ($employee === null || !$this->isGranted(EmployeeRole::Manager->value)) {
            $this->addFlash('danger', "Vous n'avez pas accès à cette fonction.");
            return $this->redirectToRoute('app_employee_index');
        }

        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($employee);
            $manager->flush();

            return $this->redirectToRoute('app_employee_index');
        }

        return $this->render('employee/edit.html.twig', [
            'form' => $form->createView(),
            'employee' => $employee,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(?Employee $employee, EntityManagerInterface $manager): Response
    {
        if ($employee === null || !$this->isGranted(EmployeeRole::Manager->value)) {
            $this->addFlash('danger', "Vous n'avez pas accès à cette fonction.");
            return $this->redirectToRoute('app_employee_index');
        }

        $manager->remove($employee);
        $manager->flush();
        $this->addFlash('success', 'Employé supprimé de la liste.');

        return $this->redirectToRoute('app_employee_index');
    }
}




