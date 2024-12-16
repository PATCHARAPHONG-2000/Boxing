<?php      
require_once '../connect.php';

$Database = new Database();
$conn = $Database->connect();

try {

    $stmt = $conn->prepare("SELECT * FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);

} catch (Exception $e) {
    echo $e->getMessage();
}

