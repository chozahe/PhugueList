<?php

namespace App\Form;

use App\Dto\TaskFilterDto;
use App\Enum\TaskStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFilterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choices' => TaskStatus::cases(),
                'choice_label' => fn(TaskStatus $status) => match ($status) {
                    TaskStatus::NEW => 'Новая',
                    TaskStatus::IN_PROGRESS => 'В работе',
                    TaskStatus::COMPLETED => 'Выполнена',
                },
                'required' => false,
                'placeholder' => 'Любой статус',
            ])
            ->add('fromDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Срок от',
            ])
            ->add('toDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Срок до',
            ])
            ->add('onlyOverdue', CheckboxType::class, [
                'required' => false,
                'label' => 'Только просроченные',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TaskFilterDto::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
