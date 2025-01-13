<?php
include_once '../includes/connect.php';

try {
    // Get active match ID from match_fights where isActive = 1
    $matchQuery = "SELECT id FROM sport_person WHERE isActive = 1 LIMIT 1";
    $matchStmt = $conn->query($matchQuery);
    $matchResult = $matchStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$matchResult) {
        throw new Exception('No active match found');
    }
    
    $matchId = $matchResult['id'];
    
    // Get scores for the active match
    $query = "SELECT * FROM data_score WHERE id = :match_id";
    $stmt = $conn->prepare($query);
    $stmt->execute(['match_id' => $matchId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        // Calculate total scores for each judge
        $totalScores = [
            'Score_JR1' => 0,
            'Score_JR2' => 0,
            'Score_JR3' => 0,
            'Score_JB1' => 0,
            'Score_JB2' => 0,
            'Score_JB3' => 0
        ];

        // Loop through rounds 1-5
        for ($round = 1; $round <= 5; $round++) {
            // Red side
            for ($judge = 1; $judge <= 3; $judge++) {
                $fieldName = "R{$round}_JR{$judge}";
                if (isset($result[$fieldName]) && is_numeric($result[$fieldName])) {
                    $totalScores["Score_JR{$judge}"] += (int)$result[$fieldName];
                }
            }
            
            // Blue side
            for ($judge = 1; $judge <= 3; $judge++) {
                $fieldName = "R{$round}_JB{$judge}";
                if (isset($result[$fieldName]) && is_numeric($result[$fieldName])) {
                    $totalScores["Score_JB{$judge}"] += (int)$result[$fieldName];
                }
            }
        }

        // Add total scores to result array
        $result = array_merge($result, $totalScores);

        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No scores found for this match'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 