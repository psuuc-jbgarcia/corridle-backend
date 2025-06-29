<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require '../connection.php'; // your DB connection

$store_id = $_GET['store_id'] ?? '';

if (empty($store_id)) {
    echo json_encode(["success" => false, "message" => "Store ID missing"]);
    exit;
}

$stmt = $conn->prepare("SELECT business_name, business_logo FROM stores WHERE store_id = ?");
$stmt->bind_param("s", $store_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $store = $result->fetch_assoc();
    echo json_encode(["success" => true, "store" => $store]);
} else {
    echo json_encode(["success" => false, "message" => "Store not found"]);
}
?>
