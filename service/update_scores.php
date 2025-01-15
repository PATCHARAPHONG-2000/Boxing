<?php
include_once '../includes/connect.php';

try {
    // Get request body
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['scores']) || !isset($data['round'])) {
        throw new Exception('Invalid data received');
    }
    
    // Get active match ID
    $matchQuery = "SELECT id FROM sport_person WHERE isActive = 1 LIMIT 1";
    $matchStmt = $conn->query($matchQuery);
    $matchResult = $matchStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$matchResult) {
        throw new Exception('No active match found');
    }
    
    $matchId = $matchResult['id'];
    
    // Start transaction
    $conn->beginTransaction();
    
    // Update round scores
    $updateFields = [];
    $params = ['match_id' => $matchId];
    
    foreach ($data['scores'] as $field => $value) {
        $updateFields[] = "$field = :$field";
        $params[$field] = $value;
    }
    
    $query = "UPDATE data_score SET " . implode(', ', $updateFields) . " WHERE id = :match_id";
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    
    // Get all scores for this match to calculate totals
    $scoreQuery = "SELECT * FROM data_score WHERE id = :match_id";
    $scoreStmt = $conn->prepare($scoreQuery);
    $scoreStmt->execute(['match_id' => $matchId]);
    $scores = $scoreStmt->fetch(PDO::FETCH_ASSOC);
    
    // Calculate total scores
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
            if (isset($scores[$fieldName]) && is_numeric($scores[$fieldName])) {
                $totalScores["Score_JR{$judge}"] += (int)$scores[$fieldName];
            }
        }
        
        // Blue side
        for ($judge = 1; $judge <= 3; $judge++) {
            $fieldName = "R{$round}_JB{$judge}";
            if (isset($scores[$fieldName]) && is_numeric($scores[$fieldName])) {
                $totalScores["Score_JB{$judge}"] += (int)$scores[$fieldName];
            }
        }
    }
    
    // Update total scores
    $updateTotalQuery = "UPDATE data_score SET 
        Score_JR1 = :score_jr1,
        Score_JR2 = :score_jr2,
        Score_JR3 = :score_jr3,
        Score_JB1 = :score_jb1,
        Score_JB2 = :score_jb2,
        Score_JB3 = :score_jb3
        WHERE id = :match_id";
        
    $stmt = $conn->prepare($updateTotalQuery);
    $stmt->execute([
        'match_id' => $matchId,
        'score_jr1' => $totalScores['Score_JR1'],
        'score_jr2' => $totalScores['Score_JR2'],
        'score_jr3' => $totalScores['Score_JR3'],
        'score_jb1' => $totalScores['Score_JB1'],
        'score_jb2' => $totalScores['Score_JB2'],
        'score_jb3' => $totalScores['Score_JB3']
    ]);
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Scores updated successfully'
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 