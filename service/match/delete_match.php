<?php
require_once '../../includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    try {
        $id = $_POST['id'];
        
        // ดึงข้อมูลรูปภาพก่อนลบ
        $sql = "SELECT image_red, image_blue FROM sport_person WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        $images = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // ลบข้อมูลจากฐานข้อมูล
        $sql = "DELETE FROM sport_person WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([':id' => $id]);
        
        if ($result) {
            // ลบไฟล์รูปภาพ
            if ($images['image_red']) {
                @unlink('../../' . $images['image_red']);
            }
            if ($images['image_blue']) {
                @unlink('../../' . $images['image_blue']);
            }
            
            echo json_encode(['status' => 'success']);
        } else {
            throw new Exception("ไม่สามารถลบข้อมูลได้");
        }
    } catch(Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>