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


$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['email']) && isset($data['password'])) {
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);
    $userType = "Customer";
    $verification_token = bin2hex(random_bytes(32));

    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Email already exists"]);
        exit;
    }

    try {
        $user_id = random_int(100000, 999999);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "User ID error"]);
        exit;
    }

    $insert = $conn->prepare("INSERT INTO users (user_id, email, password, userType, verification_token, is_verified) VALUES (?, ?, ?, ?, ?, 0)");
    $insert->bind_param("issss", $user_id, $email, $password, $userType, $verification_token);

    if ($insert->execute()) {
        $verify_link = "http://localhost/backend/authentication/verify.php?token=$verification_token";

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'gjeric54321@gmail.com'; 
            $mail->Password   = 'bfgnybwitpwemuls';   
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;

             $mail->setFrom('gjeric54321@gmail.com', 'Corridle Support');
            $mail->addReplyTo('gjeric54321@gmail.com', 'Corridle Support');
            $mail->Subject = 'Please verify your email address';
            $mail->isHTML(true);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Verify your email address';
            $mail->Body    = "<p>Hi,</p><p>Please click the link below to verify your email:</p><a href='$verify_link'>$verify_link</a>";

            $mail->send();
            echo json_encode(["success" => true, "message" => "Verification email sent.", "user_id" => $user_id, "userType" => $userType,"email" => $email]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => "Email error: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Failed to register user"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Missing email or password"]);
}
?>
