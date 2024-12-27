<?php
// Ensure no output before headers
ob_start();

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log errors to file
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

try {
    // Include database connection
    require_once '../includes/connect.php';

    // Verify connection
    if (!isset($conn)) {
        throw new Exception('Database connection not established');
    }

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT name_match, person_red, person_blue FROM sport_person");
    $success = $stmt->execute();

    if (!$success) {
        throw new Exception('Query failed to execute');
    }

    // Fetch results
    $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Clear any previous output
    ob_clean();

    // Ensure we're sending a valid JSON array even if empty
    echo json_encode($matches ?: [], JSON_THROW_ON_ERROR);

} catch (Exception $e) {
    // Clear any previous output
    ob_clean();
    
    // Log error
    error_log("Database error: " . $e->getMessage());
    
    // Send error response
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Database error: ' . $e->getMessage()
    ], JSON_THROW_ON_ERROR);
}

// Ensure all output is sent and stop execution
ob_end_flush();
exit();
?> 