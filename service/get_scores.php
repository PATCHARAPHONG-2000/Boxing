<?php
header('Content-Type: application/json');
require_once '../includes/connect.php';

try {
    if (!isset($_POST['sport_person_id'])) {
        throw new Exception('Missing sport_person_id');
    }

    $sport_person_id = $_POST['sport_person_id'];

    // ตรวจสอบว่ามีข้อมูลหรือไม่
    $sql = "SELECT * FROM data_score WHERE sport_person_id = :sport_person_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':sport_person_id' => $sport_person_id]);
    
    if ($stmt->rowCount() === 0) {
        // ถ้าไม่มีข้อมูล ให้สร้างข้อมูลใหม่
        $sql_insert = "INSERT INTO data_score (
            sport_person_id,
            R1_JR1, R1_JR2, R1_JR3, R1_JB1, R1_JB2, R1_JB3,
            R2_JR1, R2_JR2, R2_JR3, R2_JB1, R2_JB2, R2_JB3,
            R3_JR1, R3_JR2, R3_JR3, R3_JB1, R3_JB2, R3_JB3,
            R4_JR1, R4_JR2, R4_JR3, R4_JB1, R4_JB2, R4_JB3,
            R5_JR1, R5_JR2, R5_JR3, R5_JB1, R5_JB2, R5_JB3
        ) VALUES (
            :sport_person_id,
            null, null, null, null, null, null,
            null, null, null, null, null, null,
            null, null, null, null, null, null,
            null, null, null, null, null, null,
            null, null, null, null, null, null
        )";
        
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->execute([':sport_person_id' => $sport_person_id]);
        
        // ดึงข้อมูลที่เพิ่งสร้าง
        $stmt = $conn->prepare($sql);
        $stmt->execute([':sport_person_id' => $sport_person_id]);
    }

    $scores = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode([
        'status' => 'success',
        'scores' => $scores
    ]);

} catch (Exception $e) {
    error_log("Error in get_scores.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?> 