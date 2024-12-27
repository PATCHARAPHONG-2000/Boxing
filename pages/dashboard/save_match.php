<?php
require_once '../../includes/connect.php';

try {
    // เริ่ม transaction
    $conn->beginTransaction();

    // บรวจสอบรหัสซ้ำ
    $sport_person_id = $_POST['sport_person_id'];
    $check_duplicate = "SELECT COUNT(*) as count FROM sport_person WHERE sport_person_id = :sport_person_id";
    $stmt_check = $conn->prepare($check_duplicate);
    $stmt_check->execute([':sport_person_id' => $sport_person_id]);
    $result = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
        throw new Exception("รหัสนักกีฬา " . $sport_person_id . " มีอยู่ในระบบแล้ว กรุณาใช้รหัสอื่น");
    }

    // บันทึกข้อมูลในตาราง data_match
    $match_name = $_POST['Match_Name'];
    
    // บันทึกชื่อแมตช์ลงในตาราง data_match
    $sql_match = "INSERT INTO data_match (name_match) VALUES (:name_match)";
    $stmt_match = $conn->prepare($sql_match);
    $stmt_match->execute([':name_match' => $match_name]);

    // จั���การรูปภาพฝ่ายแดง
    $image_red = null;
    if (isset($_FILES['image_red']) && $_FILES['image_red']['error'] === 0) {
        $upload_path = '../../uploads/';
        $file_ext = pathinfo($_FILES['image_red']['name'], PATHINFO_EXTENSION);
        $file_name = 'red_' . uniqid() . '.' . $file_ext;
        move_uploaded_file($_FILES['image_red']['tmp_name'], $upload_path . $file_name);
        $image_red = 'uploads/' . $file_name;
    }

    // จัดการรูปภาพฝ่ายน้ำเงิน
    $image_blue = null;
    if (isset($_FILES['image_blue']) && $_FILES['image_blue']['error'] === 0) {
        $upload_path = '../../uploads/';
        $file_ext = pathinfo($_FILES['image_blue']['name'], PATHINFO_EXTENSION);
        $file_name = 'blue_' . uniqid() . '.' . $file_ext;
        move_uploaded_file($_FILES['image_blue']['tmp_name'], $upload_path . $file_name);
        $image_blue = 'uploads/' . $file_name;
    }

    // บันทึกข้อมูลในตาราง sport_person พร้อมชื่อแมตช์
    $sql_person = "INSERT INTO sport_person (
        name_match, 
        person_red, 
        person_blue, 
        image_red, 
        image_blue,
        sport_person_id
    ) VALUES (
        :name_match, 
        :person_red, 
        :person_blue, 
        :image_red, 
        :image_blue,
        :sport_person_id
    )";
    
    $stmt_person = $conn->prepare($sql_person);
    $stmt_person->execute([
        ':name_match' => $match_name,
        ':person_red' => $_POST['person_red'],
        ':person_blue' => $_POST['person_blue'],
        ':image_red' => $image_red,
        ':image_blue' => $image_blue,
        ':sport_person_id' => $sport_person_id
    ]);

    // เพิ่มการบันทึกข้อมูลลงในตาราง data_score
    $sql_data_score = "INSERT INTO data_score (sport_person_id) VALUES (:sport_person_id)";
    $stmt_score = $conn->prepare($sql_data_score);
    $stmt_score->execute([':sport_person_id' => $sport_person_id]);

    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'บันทึกข้อมูลเรียบร้อย'
    ]);

} catch(Exception $e) {
    // Rollback transaction ถ้าเกิดข้อผิดพลาด
    $conn->rollBack();
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>