<?php 
require_once '../connect.php';

$Database = new Database();
$conn = $Database->connect();

try {

    $stmt = $conn->prepare("SELECT * FROM sport_person");
    $stmt->execute();
    $spps = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($spps);

} catch (Exception $e) {
    echo $e->getMessage();
}

