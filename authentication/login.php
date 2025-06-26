<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

require '../connection.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['email']) && isset($data['password'])) {
    $email = $data['email'];
    $password = $data['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "This account is not registered."]);
        exit;
    }

    $user = $result->fetch_assoc();

    if ($user['is_verified'] == 0) {
        echo json_encode(["success" => false, "message" => "Please verify your email before logging in."]);
        exit;
    }

    if (password_verify($password, $user['password'])) {
        echo json_encode([
            "success" => true,
            "message" => "Login successful.",
            "userType" => $user['userType'],
            "userId" => $user['user_id'],
            "has_store_info" => (int)$user['has_store_info'] // Must be this
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid email or password."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Missing email or password"]);
}
?>
