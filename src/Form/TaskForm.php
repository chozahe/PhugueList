<?php

namespace App\Form;

use App\Dto\TaskFormDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class TaskForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название задачи',
            ])
            ->add('description', TextType::class, [
                'label' => 'Описание',
                'required' => false,
            ])
            ->add('deadline', DateTimeType::class, [
                'label' => 'Срок выполнения',
                'widget' => 'single_text',
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TaskFormDto::class,
        ]);
    }
}
