<?php
// เปิด error reporting เพื่อ debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตั้งค่า headers
header('Content-Type: application/json');

// ทดสอบการเชื่อมต่อฐานข้อมูล
try {
    require_once '../../includes/connect.php';
    
    // ตรวจสอบข้อมูล
    if (!isset($_POST['id']) || !isset($_POST['isActive'])) {
        throw new Exception("Missing required parameters");
    }

    $id = (int)$_POST['id'];
    $isActive = (int)$_POST['isActive'];

    // เช้ PDO prepared statement
    $sql = "UPDATE sport_person SET isActive = :isActive WHERE id = :id";
    $stmt = $conn->prepare($sql);
    
    // bind values
    $stmt->bindParam(':isActive', $isActive, PDO::PARAM_INT);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    // execute
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'อัพเดทสถานะเรียบร้อยแล้ว'
        ]);
    } else {
        throw new Exception("Failed to update status");
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 