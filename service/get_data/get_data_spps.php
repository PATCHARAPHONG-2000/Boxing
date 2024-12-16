<?php 
require_once '../connect.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

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