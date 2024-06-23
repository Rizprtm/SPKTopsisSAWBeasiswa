<?php
namespace App\Services;

use App\Models\Alternative;
use App\Models\AlternativeScore;
use App\Models\CriteriaWeight;

class SawService
{

  public function calculateSaw($periode_id)
  {
    // Fetch data
    $scores = $this->fetchScores($periode_id);
    $criteriaweights = CriteriaWeight::all();
    $alternatives = $this->fetchAlternatives($periode_id);

    // Calculate weighted score for each alternative
    $weightedScores = $this->weightNormalizedScores($scores, $criteriaweights);

    // Rank alternatives based on weighted score (descending order)
    $rankings = $this->calculatePreferenceValues($weightedScores);

    return [
      'scores' => $scores,
      'criteriaweights' => $criteriaweights,
      'alternatives' => $alternatives,
      'weightedScores' => $weightedScores,
      'rankings' => $rankings,
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
        $criteria = $criteriaweights->where('id', $criteria_id)->first();
        if (!$criteria) {
            continue; // Skip if criteria is not found
        }

        $max = $group->max('rating');
        $min = $group->min('rating');

        foreach ($group as $item) {
            if ($criteria->type == 'benefit') {
                $normalizedScores[$item->alternative_id][$criteria_id] = $item->rating / $max;
            } else {
                $normalizedScores[$item->alternative_id][$criteria_id] = $min / $item->rating;
            }
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

  public function preferences($normalizedScores, $criteriaweights)
  {
      $weightedScores = [];
  
      foreach ($normalizedScores as $alternative_id => $scores) {
          foreach ($scores as $criteria_id => $normalizedScore) {
              $criteria = $criteriaweights->where('id', $criteria_id)->first();
              if (!$criteria) {
                  error_log("Criteria ID not found: $criteria_id");
                  continue; // Skip if criteria is not found
              }
  
              $weight = $criteria->weight;
              $weightedScores[$alternative_id][$criteria_id] = $normalizedScore * $weight;
          }
      }
  
      $preferenceValues = [];
      foreach ($weightedScores as $alternative_id => $scores) {
          $preferenceValues[$alternative_id] = array_sum($scores);
      }
  
      arsort($preferenceValues); // Sort in descending order
  
      return $preferenceValues;
  }
}
