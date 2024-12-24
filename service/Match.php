<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    require_once '../config/db.php';
    
    // ตรวจสอบและสร้างตาราง data_score ถ้ายังไม่มี
    $conn->exec("DROP TABLE IF EXISTS data_score");
    $conn->exec("CREATE TABLE data_score (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sport_person_id INT,
        name_match VARCHAR(255),
        person_red VARCHAR(255),
        person_blue VARCHAR(255),
        R1_JR1 INT DEFAULT 0,
        R1_JB1 INT DEFAULT 0,
        R1_JR2 INT DEFAULT 0,
        R1_JB2 INT DEFAULT 0,
        R1_JR3 INT DEFAULT 0,
        R1_JB3 INT DEFAULT 0,
        R2_JR1 INT DEFAULT 0,
        R2_JB1 INT DEFAULT 0,
        R2_JR2 INT DEFAULT 0,
        R2_JB2 INT DEFAULT 0,
        R2_JR3 INT DEFAULT 0,
        R2_JB3 INT DEFAULT 0,
        R3_JR1 INT DEFAULT 0,
        R3_JB1 INT DEFAULT 0,
        R3_JR2 INT DEFAULT 0,
        R3_JB2 INT DEFAULT 0,
        R3_JR3 INT DEFAULT 0,
        R3_JB3 INT DEFAULT 0,
        R4_JR1 INT DEFAULT 0,
        R4_JB1 INT DEFAULT 0,
        R4_JR2 INT DEFAULT 0,
        R4_JB2 INT DEFAULT 0,
        R4_JR3 INT DEFAULT 0,
        R4_JB3 INT DEFAULT 0,
        R5_JR1 INT DEFAULT 0,
        R5_JB1 INT DEFAULT 0,
        R5_JR2 INT DEFAULT 0,
        R5_JB2 INT DEFAULT 0,
        R5_JR3 INT DEFAULT 0,
        R5_JB3 INT DEFAULT 0,
        Score_JR1 INT DEFAULT 0,
        Score_JB1 INT DEFAULT 0,
        Score_JR2 INT DEFAULT 0,
        Score_JB2 INT DEFAULT 0,
        Score_JR3 INT DEFAULT 0,
        Score_JB3 INT DEFAULT 0,
        create_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // เชิ่มข้อมูลตัวอย่าง
    $conn->exec("INSERT INTO data_score 
        (id, sport_person_id, name_match, person_red, person_blue) VALUES 
        (1, 1, 'คู่ที่ 1', 'นักมวยแดง', 'นักมวยน้ำเงิน'),
        (2, 2, 'คู่ที่ 2', 'นักมวยเขียว', 'นักมวยเหลือง')
    ");
    
    // ดึงข้อมูลคู่มวย
    $sql = "SELECT * FROM data_score ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'error' => false,
        'data' => $matches
    ]);
    
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Failed to load matches: ' . $e->getMessage()
    ]);
}
?>