<?php
ob_start(); // Clean any output before JSON

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

require '../connection.php';

// Debug: Log raw input and parsed data
$rawInput = file_get_contents("php://input");
file_put_contents("php_debug_log.txt", "RAW INPUT:\n$rawInput\n", FILE_APPEND);

$data = json_decode($rawInput, true);
file_put_contents("php_debug_log.txt", "PARSED DATA:\n" . print_r($data, true) . "\n", FILE_APPEND);

// Check required fields
if (
    !isset($data['user_uid']) ||
    !isset($data['firstName']) ||
    !isset($data['lastName']) ||
    !isset($data['phoneNumber']) ||
    !isset($data['dateOfBirth']) ||
    !isset($data['email']) ||
    !isset($data['userType'])
) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$user_uid = $conn->real_escape_string($data['user_uid']);
$firstName = $conn->real_escape_string($data['firstName']);
$middleName = $conn->real_escape_string($data['middleName'] ?? '');
$lastName = $conn->real_escape_string($data['lastName']);
$phoneNumber = $conn->real_escape_string($data['phoneNumber']);
$dateOfBirth = $conn->real_escape_string($data['dateOfBirth']);
$email = $conn->real_escape_string($data['email']);
$userType = $conn->real_escape_string($data['userType']);

$insert_sql = "INSERT INTO user_info (
    user_uid, first_name, middle_name, last_name,
    phone_number, date_of_birth, email
) VALUES (
    '$user_uid', '$firstName', '$middleName', '$lastName',
    '$phoneNumber', '$dateOfBirth', '$email'
)";

$update_sql = "UPDATE users SET userType = '$userType' WHERE user_id = '$user_uid'";

// Insert and update
if ($conn->query($insert_sql) === TRUE) {
    if ($conn->query($update_sql) === TRUE) {
        ob_end_clean();
        echo json_encode(['success' => true, 'message' => 'Information saved successfully']);
    } else {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'User update failed: ' . $conn->error]);
    }
} else {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Insert failed: ' . $conn->error]);
}

$conn->close();
exit;
