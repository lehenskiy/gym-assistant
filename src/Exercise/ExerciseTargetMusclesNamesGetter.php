<?php

declare(strict_types=1);

namespace App\Exercise;

use App\Shared\Entity\Exercise;
use App\Shared\Entity\ExerciseMuscle;

class ExerciseTargetMusclesNamesGetter
{
    public static function getTargetMusclesNamesForExercise(Exercise $exercise): array
    {
        $targetMusclesNames = [];

        /**@var $targetMuscle ExerciseMuscle */
        foreach ($exercise->getTargetMuscles() as $targetMuscle) {
            $targetMusclesNames[] = $targetMuscle->getMuscle()->fullName();
        }

        return $targetMusclesNames;
    }
}
