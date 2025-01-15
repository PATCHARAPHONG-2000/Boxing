<?php
// ป้องกันการแสดงผล error หรือ warning ที่อาจมี HTML
ini_set('display_errors', 0);
error_reporting(0);

// ตั้งค่า header เป็น JSON
header('Content-Type: application/json; charset=utf-8');

// เคลียร์ buffer เก่าทั้งหมด
if (ob_get_length()) ob_clean();

require_once '../../includes/connect.php';

try {
    // เริ่ม transaction
    $conn->beginTransaction();
    
    // ลบข้อมูลจากตารางต่างๆ
    $tables = ['data_score', 'sport_person', 'data_match'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $conn->prepare("DELETE FROM $table");
            $stmt->execute();
            $conn->exec("ALTER TABLE $table AUTO_INCREMENT = 1");
        } catch (PDOException $e) {
            throw new Exception("ไม่สามารถลบข้อมูลจากตาราง $table: " . $e->getMessage());
        }
    }
    
    // ยืนยัน transaction
    if ($conn->inTransaction()) {
        $conn->commit();
    }
    
    $response = [
        'status' => 'success',
        'message' => 'ลบข้อมูลทั้งหมดเรียบร้อยแล้ว'
    ];
    
} catch (Exception $e) {
    // ถ้ามี transaction ที่กำลังทำงานอยู่ ให้ rollback
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    $response = [
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ];
}

// ส่ง JSON response
echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;
?> 