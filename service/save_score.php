<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // รับค่าจาก FormData
        $sport_person_id = $_POST['sport_person_id'];
        $round = $_POST['round'];
        
        // กำหนดชื่อคอลัมน์ตามยกที่บันทึก
        $prefix = "R{$round}_";
        
        // เช็คว่ามีข้อมูลแล้วหรือไม่
        $check = $conn->prepare("SELECT id FROM data_score WHERE id = ?");
        $check->execute([$sport_person_id]);
        
        if ($check->rowCount() > 0) {
            // อัพเดทข้อมูลที่มีอยู่
            $sql = "UPDATE data_score SET 
                {$prefix}JR1 = :red_j1,
                {$prefix}JB1 = :blue_j1,
                {$prefix}JR2 = :red_j2,
                {$prefix}JB2 = :blue_j2,
                {$prefix}JR3 = :red_j3,
                {$prefix}JB3 = :blue_j3
                WHERE id = :sport_person_id";
        } else {
            throw new Exception('ไม่พบข้อมูลคู่มวยที่เลือก');
        }

        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([
            'sport_person_id' => $sport_person_id,
            'red_j1' => $_POST['red_judge1'] ?? 0,
            'blue_j1' => $_POST['blue_judge1'] ?? 0,
            'red_j2' => $_POST['red_judge2'] ?? 0,
            'blue_j2' => $_POST['blue_judge2'] ?? 0,
            'red_j3' => $_POST['red_judge3'] ?? 0,
            'blue_j3' => $_POST['blue_judge3'] ?? 0
        ]);

        if ($result) {
            // คำนวณคะแนนรวม
            $update_total = "UPDATE data_score SET 
                Score_JR1 = (COALESCE(R1_JR1,0) + COALESCE(R2_JR1,0) + COALESCE(R3_JR1,0) + COALESCE(R4_JR1,0) + COALESCE(R5_JR1,0)),
                Score_JB1 = (COALESCE(R1_JB1,0) + COALESCE(R2_JB1,0) + COALESCE(R3_JB1,0) + COALESCE(R4_JB1,0) + COALESCE(R5_JB1,0)),
                Score_JR2 = (COALESCE(R1_JR2,0) + COALESCE(R2_JR2,0) + COALESCE(R3_JR2,0) + COALESCE(R4_JR2,0) + COALESCE(R5_JR2,0)),
                Score_JB2 = (COALESCE(R1_JB2,0) + COALESCE(R2_JB2,0) + COALESCE(R3_JB2,0) + COALESCE(R4_JB2,0) + COALESCE(R5_JB2,0)),
                Score_JR3 = (COALESCE(R1_JR3,0) + COALESCE(R2_JR3,0) + COALESCE(R3_JR3,0) + COALESCE(R4_JR3,0) + COALESCE(R5_JR3,0)),
                Score_JB3 = (COALESCE(R1_JB3,0) + COALESCE(R2_JB3,0) + COALESCE(R3_JB3,0) + COALESCE(R4_JB3,0) + COALESCE(R5_JB3,0))
                WHERE id = :sport_person_id";
            
            $stmt_total = $conn->prepare($update_total);
            $stmt_total->execute(['sport_person_id' => $sport_person_id]);

            echo json_encode([
                'error' => false,
                'message' => 'บันทึกคะแนนสำเร็จ'
            ]);
        } else {
            throw new Exception('ไม่สามารถบันทึกข้อมูลได้');
        }
        
    } catch (PDOException $e) {
        error_log("Save score error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ]);
    }
}
?>