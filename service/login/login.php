<?php 
header('Content-Type: application/json');
require_once '../connect.php';

$Database = new Database();
$conn = $Database->connect();

if (!$conn) {
    respondError("ไม่สามารถเชื่อมต่อฐานข้อมูลได้");
}

function respondError($message, $debugMessage = null)
{
    if ($debugMessage) {
        error_log($debugMessage);
    }
    echo json_encode(['error' => $message]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $users = $_POST['User'] ?? '';
        $password = $_POST['password'] ?? '';
    
        if (empty($users) || empty($password)) {
            respondError('ชื่อผู้ใช้หรือรหัสผ่านไม่สามารถเว้นว่างได้');
        }
    
        try {
           
            $stmt = $conn->prepare("SELECT * FROM users WHERE users = :users");
            $stmt->bindParam(':users', $users);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($user) {
             
                if (password_verify($password, $user['pass'])) {
                  
                    $_SESSION['AD_ID'] = $user['id'];
                    $_SESSION['AD_USERNAME'] = $user['users'];
    
                   
                    if ($_SESSION['AD_ROLE']) {
                        echo json_encode([
                            'status' => true,
                            'role' => $_SESSION['AD_ROLE'],
                            'message' => 'Admin Login Success'
                        ]);
                        exit();
                    } else {
                        respondError('ไม่มีสิทธิ์เข้าใช้งาน');
                    }
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