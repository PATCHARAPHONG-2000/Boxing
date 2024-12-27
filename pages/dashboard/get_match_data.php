<?php
require_once '../../includes/connect.php';

try {
    $sql = "SELECT id, person_red, person_blue, image_red, image_blue, IsActive 
            FROM sport_person 
            ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($result as &$row) {
        $row['isActive'] = (int)$row['IsActive'];
    }
    
    echo json_encode(['data' => $result]);
} catch(Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 