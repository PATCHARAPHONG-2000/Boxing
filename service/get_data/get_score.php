<?php 
require_once '../connect.php';

$Database = new Database();
$conn = $Database->connect();

try {

    $stmt = $conn->prepare("SELECT * FROM data_score");
    $stmt->execute();
    $score = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($score);

} catch (Exception $e) {
    echo $e->getMessage();
}

