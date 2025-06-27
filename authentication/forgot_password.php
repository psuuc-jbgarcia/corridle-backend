<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
// Update this path based on your screenshot
require 'PHPMailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'PHPMailer/vendor/phpmailer/phpmailer/src/SMTP.php';
require 'PHPMailer/vendor/phpmailer/phpmailer/src/Exception.php';
require '../connection.php';

// Parse JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email'])) {
    echo json_encode(['success' => false, 'message' => 'Email is required.']);
    exit;
}

$email = trim($data['email']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

// Check if email exists in users table
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'DB error: ' . $conn->error]);
    exit;
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Email not found.']);
    exit;
}

// Generate token and expiry
$token = bin2hex(random_bytes(32));
$expiresAt = date("Y-m-d H:i:s", strtotime("+1 hour"));

// Insert into password_resets table
$stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare insert: ' . $conn->error]);
    exit;
}
$stmt->bind_param("sss", $email, $token, $expiresAt);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to insert reset token: ' . $stmt->error]);
    exit;
}

$resetLink = "http://192.168.100.177/backend/authentication/reset_password.php?token=$token"; 
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'gjeric54321@gmail.com'; 
    $mail->Password   = 'bfgnybwitpwemuls'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;


    $mail->setFrom('gjeric54321@gmail.com', 'Corridle Support');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Reset Your Password';
    $mail->Body    = "
        <h3>Password Reset Request</h3>
        <p>Click the link below to reset your password:</p>
        <p><a href='$resetLink'>$resetLink</a></p>
        <p>This link will expire in 1 hour.</p>
    ";

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Reset link sent successfully.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
}
?>
