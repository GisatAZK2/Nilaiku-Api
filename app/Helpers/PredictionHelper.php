<?php

namespace App\Helpers;

class PredictionHelper
{
    public static function convertToModelFormat(array $validatedData): array
    {
        return [
            'attendance'          => $validatedData['attendance'],
            'hours_studied'       => $validatedData['hours_studied'],
            'previous_scores'     => $validatedData['previous_scores'],
            'sleep_hours'         => $validatedData['sleep_hours'],
            'tutoring_sessions'   => $validatedData['tutoring_sessions'],
            'peer_influence'      => $validatedData['peer_influence'] === 'positive' ? 0 : ($validatedData['peer_influence'] === 'neutral' ? 1 : 2),
            'motivation_level'    => $validatedData['motivation_level'] === 'low' ? 0 : ($validatedData['motivation_level'] === 'medium' ? 1 : 2),
            'teacher_quality'     => $validatedData['teacher_quality'] === 'low' ?  : ($validatedData['teacher_quality'] === 'medium' ? 1 : 2),
            'access_to_resources' => $validatedData['access_to_resources'] === 'low' ? 0 : ($validatedData['access_to_resources'] === 'medium' ? 1 : 2),
        ];
    }
}
