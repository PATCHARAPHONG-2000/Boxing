<?php
require_once '../../includes/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = $_POST['id'];
        $person_red = $_POST['person_red'];
        $person_blue = $_POST['person_blue'];
        
        // จัดการรูปภาพมุมแดง
        $image_red = null;
        if(isset($_FILES['image_red']) && $_FILES['image_red']['error'] === 0) {
            $fileExt = pathinfo($_FILES['image_red']['name'], PATHINFO_EXTENSION);
            $newFileName = 'red_' . uniqid() . '.' . $fileExt;
            $image_red = 'uploads/' . $newFileName;
            move_uploaded_file($_FILES['image_red']['tmp_name'], '../../' . $image_red);
        }

        // จัดการรูปภาพมุมน้ำเงิน
        $image_blue = null;
        if(isset($_FILES['image_blue']) && $_FILES['image_blue']['error'] === 0) {
            $fileExt = pathinfo($_FILES['image_blue']['name'], PATHINFO_EXTENSION);
            $newFileName = 'blue_' . uniqid() . '.' . $fileExt;
            $image_blue = 'uploads/' . $newFileName;
            move_uploaded_file($_FILES['image_blue']['tmp_name'], '../../' . $image_blue);
        }

        // สร้าง SQL query
        $sql = "UPDATE sport_person SET person_red = :person_red, person_blue = :person_blue";
        $params = [
            ':id' => $id,
            ':person_red' => $person_red,
            ':person_blue' => $person_blue
        ];

        // เพิ่มการอัพเดทรูปถ้ามีการอัพโหลดใหม่
        if ($image_red) {
            $sql .= ", image_red = :image_red";
            $params[':image_red'] = $image_red;
        }
        if ($image_blue) {
            $sql .= ", image_blue = :image_blue";
            $params[':image_blue'] = $image_blue;
        }

        $sql .= " WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $result = $stmt->execute($params);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'อัพเดทข้อมูลสำเร็จ']);
        } else {
            throw new Exception("ไม่สามารถอัพเดทข้อมูลได้");
        }
    } catch(Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?> 