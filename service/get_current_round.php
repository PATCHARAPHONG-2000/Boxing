<?php
header('Content-Type: application/json');
include_once '../includes/connect.php';

try {
    // ดึง ID ของการแข่งขันที่กำลังแข่งอยู่
    $matchQuery = "SELECT id, round FROM sport_person WHERE isActive = 1 LIMIT 1";
    $matchStmt = $conn->query($matchQuery);
    $matchResult = $matchStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($matchResult) {
        echo json_encode([
            'success' => true,
            'round' => (int)$matchResult['round']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'ไม่พบการแข่งขันที่กำลังดำเนินอยู่'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการเชื่อมต่อกับฐานข้อมูล: ' . $e->getMessage()
    ]);
}

$conn = null;
?> 