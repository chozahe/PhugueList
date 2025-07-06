<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class TaskFormDto
{
    #[Assert\NotBlank(message: 'Название задачи не может быть пустым')]
    #[Assert\Length(max: 255, maxMessage: 'Максимальная длина — 255 символов')]
    public ?string $name = null;

    #[Assert\Length(
        max: 255,
        maxMessage: 'Описание не должно превышать 255 символов'
    )]
    public ?string $description = null;

    #[Assert\NotNull(message: 'Пожалуйста, укажите срок выполнения')]
    #[Assert\GreaterThan('now', message: 'Срок должен быть в будущем')]
    public ?\DateTimeImmutable $deadline = null;
}
