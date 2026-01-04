<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Enum\EmployeeRole;
use App\Enum\EmployeeStatus;
use App\Form\EmployeeRegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class AuthController extends AbstractController
{
    #[Route('/home', name: 'app_auth_welcome')]
    public function index(): Response
    {
        return $this->render('auth/welcome.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }

    #[Route('/login', name: 'app_auth_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // récupérer l'erreur de connexion si elle existe
        $error = $authenticationUtils->getLastAuthenticationError();

        // récupérer le dernier email saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/signin', name: 'app_auth_signin')]
    public function signin(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $employee = new Employee();

        $form = $this->createForm(EmployeeRegistrationType::class, $employee);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe
            $employee->setPassword(
                $passwordHasher->hashPassword($employee, $employee->getPassword())
            );

            // Initialisation automatique de l'entrée
            $employee->setEntryDate(new \DateTimeImmutable());
            $employee->setStatus(EmployeeStatus::CDI);
            $employee->setRoles([EmployeeRole::Collaborateur->value]); // rôle par défaut

            $manager->persist($employee);
            $manager->flush();

            return $this->redirectToRoute('app_project_index');
        }

        return $this->render('auth/signin.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/logout', name: 'app_auth_logout')]
    public function logout(): void
    {
        // Ce code ne sera jamais exécuté
        throw new \LogicException('This should never be reached!');
    }

}
