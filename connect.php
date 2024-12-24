<?php
// กำหนดค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";        // ชื่อผู้ใช้ฐานข้อมูล (ค่าเริ่มต้นของ XAMPP)
$password = "";            // รหัสผ่าน (ค่าเริ่มต้นของ XAMPP)
$dbname = "db_boxing";     // ชื่อฐานข้อมูล

try {
    // สร้างการเชื่อมต่อ PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // ตั้งค่าการใช้งาน PDO ให้แสดงข้อผิดพลาดหากมี
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // ตั้งค่าชุดอักขระให้เป็น UTF-8
    $conn->exec("set names utf8mb4");

} catch (PDOException $e) {
    // หากเกิดข้อผิดพลาดจะหยุดการทำงานและแสดงข้อความ
    echo "Connection failed: " . $e->getMessage();
}
?>