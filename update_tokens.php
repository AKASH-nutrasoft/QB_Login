<?php
require_once "functions.php";

session_start(); // Ensure session is started


// Ensure clean output
header('Content-Type: application/json');

$accessToken = $_SESSION['sessionAccessToken'];

try {
    // Update the database with token information
    connect_db_customer(
        $accessToken->getAccessToken(),
        $accessToken->getAccessTokenExpiresAt(),
        $accessToken->getRefreshToken(),
        $accessToken->getRefreshTokenExpiresAt()
    );

    // Return success response
    echo json_encode(['success' => true, 'message' => 'Tokens updated successfully']);
} catch (Exception $e) {
    // Log error details to a file
    error_log("Error in update_tokens.php: " . $e->getMessage(), 3, 'error_log.txt');

    // Return error response as JSON
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>
