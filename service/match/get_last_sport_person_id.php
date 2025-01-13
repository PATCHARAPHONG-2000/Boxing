<?php
require_once '../../includes/connect.php';

try {
    // ดึงเลขลำดับล่าสุดจากฐานข้อมูล โดยใช้ตัวเลขที่อยู่หลัง TKS
    $sql = "SELECT MAX(CAST(SUBSTRING(sport_person_id, 4) AS UNSIGNED)) as max_num FROM sport_person";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // ถ้าไม่มีข้อมูลให้เริ่มที่ 1 ถ้ามีให้เพิ่มขึ้น 1
    $number = ($row['max_num'] === null) ? 1 : $row['max_num'] + 1;

    // สร้าง formatted ID
    $formatted_id = 'TKS' . str_pad($number, 3, '0', STR_PAD_LEFT);

    echo json_encode([
        'status' => 'success', 
        'last_number' => $number,
        'formatted_id' => $formatted_id
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'ไม่สามารถดึงรหัสล่าสุดได้: ' . $e->getMessage()
    ]);
}
?> 