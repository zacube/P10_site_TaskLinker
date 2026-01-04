<?php

namespace App\Enum;

enum TaskStatus: string
{
    case ToDo = 'todo';
    case Doing = 'doing';
    case Done = 'done';

    public function getLabel(): string
    {
        return match ($this) {
            self::ToDo => 'À faire',
            self::Doing => 'En cours',
            self::Done => 'Terminé',
        };
    }
}
