<?php
require_once '../connect.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


$Database = new Database();
$conn = $Database->connect();

if (!$conn) {
    respondError("ไม่สามารถเชื่อมต่อฐานข้อมูลได้");
}

function respondError($message, $debugMessage = null) {
    if ($debugMessage) {
        error_log($debugMessage); // บันทึก Log แต่ไม่แสดงข้อความบนหน้าจอ
    }
    echo json_encode(['status' => false, 'error' => $message]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = $_POST['User'] ?? null;
    $password = $_POST['password'] ?? null;

    if (empty($users) || empty($password)) {
        respondError('ชื่อผู้ใช้หรือรหัสผ่านไม่สามารถเว้นว่างได้');
    }

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE users = :users");
        $stmt->bindParam(':users', $users);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // ตรวจสอบว่า 'password' มีค่าและใช้งานได้
            if (!empty($user['password']) && password_verify($password, $user['password'])) {
                $_SESSION['AD_ID'] = $user['id'];
                $_SESSION['AD_USERNAME'] = $user['users'];
                $_SESSION['AD_ROLE'] = $user['role'] ?? '';

                echo json_encode([
                    'status' => true,
                    'role' => $_SESSION['AD_ROLE'],
                    'message' => 'Login Success'
                ]);
            } else {
                respondError('รหัสผ่านไม่ถูกต้อง');
            }
        } else {
            respondError('ไม่มีชื่อผู้ใช้นี้ในระบบ');
        }
    } catch (PDOException $e) {
        respondError("เกิดข้อผิดพลาดในระบบ โปรดลองใหม่อีกครั้ง", $e->getMessage());
    }
}
