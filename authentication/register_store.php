<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require '../connection.php';

file_put_contents('debug.log', "---- NEW REQUEST ----\n", FILE_APPEND);
file_put_contents('debug.log', "POST: " . print_r($_POST, true), FILE_APPEND);
file_put_contents('debug.log', "FILES: " . print_r($_FILES, true), FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $store_id = $_POST['store_id'] ?? '';
    $user_uid = $_POST['user_uid'] ?? '';
    $business_name = $_POST['business_name'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $email = $_POST['email'] ?? '';
    $category = $_POST['category'] ?? '';
    $description = $_POST['description'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';

    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $ownership_proof_path = null;
    if (isset($_FILES['ownership_proof']) && $_FILES['ownership_proof']['error'] === UPLOAD_ERR_OK) {
        $filename = uniqid() . "_" . basename($_FILES["ownership_proof"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["ownership_proof"]["tmp_name"], $target_file)) {
            $ownership_proof_path = $target_file;
        } else {
            echo json_encode(["success" => false, "message" => "❌ Failed to upload image."]);
            exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO stores (store_id, user_uid, business_name, phone_number, email, category, description, ownership_proof, postal_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $store_id, $user_uid, $business_name, $phone_number, $email, $category, $description, $ownership_proof_path, $postal_code);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "✅ Store registered."]);
    } else {
        echo json_encode(["success" => false, "message" => "❌ Database error: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "❌ Invalid request method."]);
}
