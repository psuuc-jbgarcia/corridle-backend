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
        $response = [
            "success" => true,
            "message" => "Login successful.",
            "userType" => $user['userType'],
            "userId" => $user['user_id'],
            "email" => $user['email'],

            "has_store_info" => (int)$user['has_store_info']
        ];

        // 🔍 If user is a Shop Owner, fetch their store_id
        if ($user['userType'] === 'Shop Owner') {
            $storeStmt = $conn->prepare("SELECT store_id FROM stores WHERE user_uid = ? LIMIT 1");
            $storeStmt->bind_param("s", $user['user_id']);
            $storeStmt->execute();
            $storeResult = $storeStmt->get_result();

            if ($storeResult->num_rows > 0) {
                $store = $storeResult->fetch_assoc();
                $response['storeId'] = $store['store_id'];
            } else {
                $response['storeId'] = null; // Still send the key if not found
            }
        }

        echo json_encode($response);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid email or password."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Missing email or password"]);
}
?>
