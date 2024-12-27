<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_boxing";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ดึงเลขลำดับล่าสุดจากฐานข้อมูล
    $sql = "SELECT sport_person_id FROM sport_person ORDER BY sport_person_id DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // ถ้ามีข้อมูล ดึงตัวเลขจาก SP-XXXXX
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastId = $row['sport_person_id'];
        $number = intval(substr($lastId, 3)); // ตัด SP- ออกและแปลงเป็นตัวเลข
    } else {
        // ถ้าไม่มีข้อมูล เริ่มที่ 0
        $number = 0;
    }

    echo json_encode(['status' => 'success', 'last_number' => $number]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?> 