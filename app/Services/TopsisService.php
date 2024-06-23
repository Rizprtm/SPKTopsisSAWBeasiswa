<?php
namespace App\Services;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\CriteriaWeight;


class TopsisService
{

    public function calculateTopsis($periode_id)
    {
        // Fetch data
        $scores = $this->fetchScores($periode_id);
        $criteriaweights = CriteriaWeight::all();
        $alternatives = $this->fetchAlternatives($periode_id);

        // Normalize scores
        $normalizedScores = $this->normalizeScores($scores, $criteriaweights);

        // Weight normalized scores
        $weightedScores = $this->weightNormalizedScores($normalizedScores, $criteriaweights);

        // Determine ideal solutions
        list($idealPositive, $idealNegative) = $this->IdealSolutions($weightedScores, $criteriaweights);

        // Calculate distances
        $distances = $this->calculateDistances($weightedScores, $idealPositive, $idealNegative);

        // Calculate preference values
        $preferenceValues = $this->calculatePreferenceValues($distances);

        return [
            'scores' => $scores,
            'criteriaweights' => $criteriaweights,
            'alternatives' => $alternatives,
            'normalizedScores' => $normalizedScores,
            'weightedScores' => $weightedScores,
            'idealPositive' => $idealPositive,
            'idealNegative' => $idealNegative,
            'distances' => $distances,
            'preferenceValues' => $preferenceValues,
        ];
    }

    public function fetchScores($periode_id)
    {
        return AlternativeScore::select(
            'alternativescores.id as id',
            'alternativescores.rating as rating',
            'alternatives.id as alternative_id',
            'criteriaweights.id as criteria_id',
            'alternatives.userId as userId',
            'criteriaweights.name as criteria',
            'criteriaweights.weight as weight',
            'alternativescores.dokumen'
        )
        ->leftJoin('alternatives', 'alternatives.id', '=', 'alternativescores.alternative_id')
        ->leftJoin('criteriaweights', 'criteriaweights.id', '=', 'alternativescores.criteria_id')
        ->where('alternativescores.periode_id', $periode_id)
        ->get();
    }

    public function fetchAlternatives($periode_id)
    {
        return Alternative::distinct()
            ->select('alternatives.id', 'alternatives.userId')
            ->leftJoin('alternativescores', 'alternatives.id', '=', 'alternativescores.alternative_id')
            ->where('alternativescores.periode_id', $periode_id)
            ->get();
    }

    public function normalizeScores($scores, $criteriaweights)
    {
        $criteriaGroups = $scores->groupBy('criteria_id');
        $normalizedScores = [];

        foreach ($criteriaGroups as $criteria_id => $group) {
            $denominator = sqrt($group->sum(function($item) {
                return $item->rating ** 2;
            }));

            foreach ($group as $item) {
                $normalizedScores[$item->alternative_id][$criteria_id] = $item->rating / $denominator;
            }
        }

        return $normalizedScores;
    }

    public function weightNormalizedScores($normalizedScores, $criteriaweights)
    {
        $weightedScores = [];

        foreach ($normalizedScores as $alternative_id => $scores) {
            foreach ($scores as $criteria_id => $normalizedScore) {
                $weight = $criteriaweights->where('id', $criteria_id)->first()->weight;
                $weightedScores[$alternative_id][$criteria_id] = $normalizedScore * $weight;
            }
        }

        return $weightedScores;
    }

    public function IdealSolutions($weightedScores, $criteriaweights)
    {
        $idealSolutions = [
            'positive' => [],
            'negative' => []
        ];

        foreach ($criteriaweights as $criteria) {
            $criteria_id = $criteria->id;
            $criteria_type = $criteria->type;

            $column = array_column($weightedScores, $criteria_id);
            if ($criteria_type == 'benefit') {
                $idealSolutions['positive'][$criteria_id] = max($column);
                $idealSolutions['negative'][$criteria_id] = min($column);
            } else {
                $idealSolutions['positive'][$criteria_id] = min($column);
                $idealSolutions['negative'][$criteria_id] = max($column);
            }
        }

        return $idealSolutions;
    }

    public function calculatedistances($weightedScores, $idealSolutions)
    {
        $distances = [];

        foreach ($weightedScores as $alternative_id => $scores) {
            $positiveDistance = 0;
            $negativeDistance = 0;

            foreach ($scores as $criteria_id => $score) {
                $positiveDistance += ($score - $idealSolutions['positive'][$criteria_id]) ** 2;
                $negativeDistance += ($score - $idealSolutions['negative'][$criteria_id]) ** 2;
            }

            $distances[$alternative_id] = [
                'positive' => sqrt($positiveDistance),
                'negative' => sqrt($negativeDistance)
            ];
        }

        return $distances;
    }

    public function calculatePreferenceValues($distances)
    {
        $preferenceValues = [];

        foreach ($distances as $alternative_id => $distance) {
            $positiveDistance = $distance['positive'];
            $negativeDistance = $distance['negative'];
            $preferenceValues[$alternative_id] = $negativeDistance / ($positiveDistance + $negativeDistance);
        }

        arsort($preferenceValues); // Sort in descending order

        return $preferenceValues;
    }
    public function calculateRanking($preferenceValues)
    {
        $rankings = array_keys($preferenceValues);

        return $rankings;
    }
}
