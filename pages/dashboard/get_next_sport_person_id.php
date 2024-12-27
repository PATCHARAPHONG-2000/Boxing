<?php
require_once '../../includes/connect.php';

function getNextSportPersonId($conn) {
    $sql = "SELECT sport_person_id FROM sport_person ORDER BY sport_person_id DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastId = $row['sport_person_id'];
        // ตัดเอาเฉพาะตัวเลข
        $number = intval(substr($lastId, 2));
        // เพิ่มค่าขึ้น 1
        $nextNumber = $number + 1;
        // สร้าง ID ใหม่
        $nextId = 'SP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    } else {
        // ถ้าไม่มีข้อมูลเลย ให้เริ่มที่ SP001
        $nextId = 'SP001';
    }
    
    return $nextId;
}

// ส่งค่า ID ถัดไปกลับไป
$nextId = getNextSportPersonId($conn);
echo json_encode(['next_id' => $nextId]);
?> 