<?php
require_once '../includes/connect.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $round = isset($data['round']) ? intval($data['round']) : null;

    if ($round === null || $round < 1 || $round > 5) {
        throw new Exception('Invalid round number');
    }

    $sql = "UPDATE sport_person
            SET round = :round 
            WHERE IsActive = 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':round', $round, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'message' => 'Round updated successfully'
    ]);

} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn = null;
?> 