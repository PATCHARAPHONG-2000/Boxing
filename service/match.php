<?php
// ป้องกัน error output
ini_set('display_errors', 0);
error_reporting(0);

// ตั้งค่า headers
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// เคลียร์ buffer เก่า
if (ob_get_length()) ob_clean();

require_once '../includes/connect.php';

try {
    // เตรียมคำสั่ง SQL
    $sql = "SELECT sport_person_id, name_match, person_red, person_blue 
            FROM sport_person 
            ORDER BY sport_person_id DESC";
            
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    // ดึงข้อมูลทั้งหมด
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // ตรวจสอบว่ามีข้อมูลหรือไม่
    if (empty($matches)) {
        echo json_encode([]);
        exit;
    }
    
    // ส่งข้อมูลกลับ
    echo json_encode($matches, JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $e) {
    // ส่ง error กลับในรูปแบบ JSON
    $error = [
        'error' => true,
        'message' => 'Database error: ' . $e->getMessage()
    ];
    echo json_encode($error);
}

// จบการทำงานทันที
exit;