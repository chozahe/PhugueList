<?php

namespace App\Dto;

use App\Enum\TaskStatus;
use Symfony\Component\Validator\Constraints as Assert;

class TaskFilterDto
{
    public ?TaskStatus $status = null;

    #[Assert\Type(\DateTimeInterface::class)]
    public ?\DateTimeInterface $fromDate = null;

    #[Assert\Type(\DateTimeInterface::class)]
    public ?\DateTimeInterface $toDate = null;

    public ?bool $onlyOverdue = false;
}
