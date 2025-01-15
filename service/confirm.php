<?php
require_once '../includes/connect.php';

try {
    $sql = "SELECT 
            person_red,
            person_blue
        FROM sport_person 
        WHERE IsActive = 1 
        LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode([
            'success' => true,
            'redName' => $result['person_red'],
            'blueName' => $result['person_blue']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No active match found'
        ]);
    }

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$conn = null;
?> 