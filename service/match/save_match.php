<?php
require_once '../../includes/connect.php';

try {
    // เริ่ม transaction
    $conn->beginTransaction();

    // หึงเลขล่าสุดโดยตรงจากฐานข้อมูล
    $sql = "SELECT MAX(CAST(SUBSTRING(sport_person_id, 4) AS UNSIGNED)) as max_num FROM sport_person";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // สร้าง formatted ID
    $number = ($row['max_num'] === null) ? 1 : $row['max_num'] + 1;
    $formatted_id = 'TKS' . str_pad($number, 3, '0', STR_PAD_LEFT);

    // บันทึกข้อมูลในตาราง data_match
    $data_match = $_POST['data_match'];
    $match_type = $_POST['Match_Type'];
    
    // บันทึกชื่อแมตช์ลงในตาราง data_match
    $sql_match = "INSERT INTO data_match (name_match) VALUES (:name_match)";
    $stmt_match = $conn->prepare($sql_match);
    $stmt_match->execute([
        ':name_match' => $_POST['data_match']
    ]);

    // จัดการรูปภาพฝ่ายแดง
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

    // บันทึกข้อมูลในตาราง sport_person พร้อมชื่อแมตช์และประเภทมวย
    $sql_person = "INSERT INTO sport_person (
        name_match, 
        person_red, 
        person_blue, 
        image_red, 
        image_blue,
        sport_person_id,
        role
    ) VALUES (
        :name_match, 
        :person_red, 
        :person_blue, 
        :image_red, 
        :image_blue,
        :sport_person_id,
        :role
    )";
    
    $stmt_person = $conn->prepare($sql_person);
    $stmt_person->execute([
        ':name_match' => $_POST['data_match'],
        ':person_red' => $_POST['person_red'],
        ':person_blue' => $_POST['person_blue'],
        ':image_red' => $image_red,
        ':image_blue' => $image_blue,
        ':sport_person_id' => $formatted_id,
        ':role' => $match_type
    ]);

    // บันทึกข้อมูลเนตาราง data_score พร้อมกับ role
    $sql_score = "INSERT INTO data_score (
        sport_person_id,
        role,
        R1_JR1, R1_JR2, R1_JR3, R1_JB1, R1_JB2, R1_JB3,
        R2_JR1, R2_JR2, R2_JR3, R2_JB1, R2_JB2, R2_JB3,
        R3_JR1, R3_JR2, R3_JR3, R3_JB1, R3_JB2, R3_JB3
    ) VALUES (
        :sport_person_id,
        :role,
        NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, NULL
    )";
    
    $stmt_score = $conn->prepare($sql_score);
    $stmt_score->execute([
        ':sport_person_id' => $formatted_id,
        ':role' => $match_type
    ]);

    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'บันทึกข้อมูลเรียบร้อย',
        'sport_person_id' => $formatted_id
    ]);

} catch(Exception $e) {
    // Rollback transaction ถ้าเกิดข้อผิดพลาด
    $conn->rollBack();
    error_log("Error in save_match.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>