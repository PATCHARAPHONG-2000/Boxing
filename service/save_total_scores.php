<?php
header('Content-Type: application/json');
require_once '../includes/connect.php';

try {
    // Debug log for incoming data
    error_log("POST data: " . print_r($_POST, true));

    // ตรวจสอบว่ามีการส่ง sport_person_id มาหรือไม่
    if (!isset($_POST['sport_person_id'])) {
        throw new Exception('Missing sport_person_id');
    }

    // แปลงค่าและทำความสะอาดข้อมูล
    $sport_person_id = trim($_POST['sport_person_id']);
    
    // ตรวจสอบว่า sport_person_id มีอยู่ในฐานข้อมูลหรือไม่
    $checkStmt = $conn->prepare("SELECT id FROM data_score WHERE sport_person_id = ?");
    $checkStmt->execute([$sport_person_id]);
    
    if ($checkStmt->rowCount() === 0) {
        // ถ้าไม่พบ ลองค้นหาด้วย id โดยตรง
        $checkStmt = $conn->prepare("SELECT id FROM data_score WHERE id = ?");
        $checkStmt->execute([$sport_person_id]);
        if ($checkStmt->rowCount() === 0) {
            throw new Exception("Invalid sport_person_id: $sport_person_id");
        }
    }

    // เตรียมข้อมูลสำหรับ update
    $updateData = [];
    $params = [];
    
    // ตรวจสอบและเก็บค่าคะแนนทั้งหมด
    for ($judge = 1; $judge <= 3; $judge++) {
        $redScore = isset($_POST["Score_JR{$judge}"]) ? floatval($_POST["Score_JR{$judge}"]) : 0;
        $blueScore = isset($_POST["Score_JB{$judge}"]) ? floatval($_POST["Score_JB{$judge}"]) : 0;
        
        $updateData[] = "Score_JR{$judge} = ?";
        $updateData[] = "Score_JB{$judge} = ?";
        $params[] = $redScore;
        $params[] = $blueScore;
    }

    // เพิ่ม sport_person_id เข้าไปใน params
    $params[] = $sport_person_id;

    // สร้าง SQL query โดยใช้ sport_person_id หรือ id
    $sql = "UPDATE data_score SET " . implode(", ", $updateData) . 
           " WHERE sport_person_id = ? OR id = ?";
    
    // เพิ่ม parameter สำหรับเงื่อนไข OR
    $params[] = $sport_person_id;
    
    // Debug log
    error_log("SQL Query: " . $sql);
    error_log("Parameters: " . print_r($params, true));

    // ทำการ update
    $stmt = $conn->prepare($sql);
    if ($stmt->execute($params)) {
        echo json_encode([
            'status' => 'success',
            'message' => 'บันทึกคะแนนรวมสำเร็จ',
            'sport_person_id' => $sport_person_id
        ]);
    } else {
        throw new Exception("Database update failed");
    }

} catch (Exception $e) {
    error_log("Save total scores error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 