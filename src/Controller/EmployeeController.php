<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/employee', name: 'app_employee_')]
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
    public function edit(Employee $employee, Request $request, EntityManagerInterface $manager): Response
    {
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
    public function delete(Employee $employee, EntityManagerInterface $manager): Response
    {
        $manager->remove($employee);
        $manager->flush();
        $this->addFlash('success', 'Employé supprimé de la liste.');

        return $this->redirectToRoute('app_employee_index');
    }
}




