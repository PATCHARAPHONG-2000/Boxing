<?php
header('Content-Type: application/json');
require_once '../includes/connect.php';

try {
    // ตรวจสอบข้อมูลที่ส่งมา
    if (!isset($_POST['sport_person_id']) || !isset($_POST['round'])) {
        throw new Exception('Missing required data');
    }

    $sport_person_id = $_POST['sport_person_id'];
    $round = $_POST['round'];

    // ตรวจสอบคะแนนที่ส่งมา
    $red_judge1 = isset($_POST['red_judge1']) ? $_POST['red_judge1'] : 0;
    $red_judge2 = isset($_POST['red_judge2']) ? $_POST['red_judge2'] : 0;
    $red_judge3 = isset($_POST['red_judge3']) ? $_POST['red_judge3'] : 0;
    $blue_judge1 = isset($_POST['blue_judge1']) ? $_POST['blue_judge1'] : 0;
    $blue_judge2 = isset($_POST['blue_judge2']) ? $_POST['blue_judge2'] : 0;
    $blue_judge3 = isset($_POST['blue_judge3']) ? $_POST['blue_judge3'] : 0;

    // ตรวจสอบว่ามีข้อมูลอยู่แล้วหรือไม่
    $check_sql = "SELECT COUNT(*) FROM data_score WHERE sport_person_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$sport_person_id]);
    $exists = $check_stmt->fetchColumn() > 0;

    if ($exists) {
        // อัพเดทข้อมูลที่มีอยู่
        $sql = "UPDATE data_score SET 
                R{$round}_JR1 = :r1,
                R{$round}_JR2 = :r2,
                R{$round}_JR3 = :r3,
                R{$round}_JB1 = :b1,
                R{$round}_JB2 = :b2,
                R{$round}_JB3 = :b3
                WHERE sport_person_id = :id";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':r1' => $red_judge1,
            ':r2' => $red_judge2,
            ':r3' => $red_judge3,
            ':b1' => $blue_judge1,
            ':b2' => $blue_judge2,
            ':b3' => $blue_judge3,
            ':id' => $sport_person_id
        ]);
    } else {
        // สร้างข้อมูลใหม่
        $sql = "INSERT INTO data_score (
                sport_person_id,
                R{$round}_JR1, R{$round}_JR2, R{$round}_JR3,
                R{$round}_JB1, R{$round}_JB2, R{$round}_JB3
            ) VALUES (
                :id,
                :r1, :r2, :r3,
                :b1, :b2, :b3
            )";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':id' => $sport_person_id,
            ':r1' => $red_judge1,
            ':r2' => $red_judge2,
            ':r3' => $red_judge3,
            ':b1' => $blue_judge1,
            ':b2' => $blue_judge2,
            ':b3' => $blue_judge3
        ]);
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'บันทึกคะแนนเรียบร้อย'
    ]);

} catch (Exception $e) {
    error_log("Error in save_score.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>