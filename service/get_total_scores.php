<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            throw new Exception('ไม่พบ ID ที่ต้องการ');
        }

        // ดึงคะแนนรวมจากฐานข้อมูล
        $sql = "SELECT 
            Score_JR1, Score_JB1,
            Score_JR2, Score_JB2,
            Score_JR3, Score_JB3
        FROM data_score 
        WHERE id = :id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $scores = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$scores) {
            throw new Exception('ไม่พบข้อมูลคะแนน');
        }

        echo json_encode([
            'error' => false,
            'scores' => $scores
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
    }
}
?> 