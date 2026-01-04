<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Task;
use App\Enum\TaskStatus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre de la tâche'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('deadline', DateType::class, [
                'label' => 'Date limite',
                'widget' => 'single_text',
                'html5' => true,
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'To Do' => TaskStatus::ToDo,
                    'Doing' => TaskStatus::Doing,
                    'Done' => TaskStatus::Done,
                ],
            ])
            ->add('holder', EntityType::class, [
                'label' => 'Membre',
                'class' => Employee::class,
                'choices' => $options['team'], // Utilise les employés passés en option
                'choice_label' => function (Employee $employee) {
                    return $employee->getFirstname() . ' ' . $employee->getName();
                },
                'placeholder' => 'Choisissez un membre', // s’affiche uniquement si aucune valeur n’est sélectionnée
                'required' => false,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'team' => [], // Valeur par défaut : tableau vide
        ]);
    }
}
