<?php
require '../connection.php';

$message = "";
$alertClass = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE verification_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        $update = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE user_id = ?");
        $update->bind_param("i", $user['user_id']);
        if ($update->execute()) {
            $message = "✅ Email verified successfully. You can now log in.";
            $alertClass = "success";
        } else {
            $message = "❌ Failed to verify email.";
            $alertClass = "danger";
        }
    } else {
        $message = "❌ Invalid or expired verification link.";
        $alertClass = "warning";
    }
} else {
    $message = "❗ No verification token provided.";
    $alertClass = "secondary";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fa;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }
        .card {
            border-radius: 15px;
            padding: 30px;
            max-width: 500px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="card shadow text-center">
        <h2 class="mb-3">Email Verification</h2>
        <div class="alert alert-<?php echo $alertClass; ?>" role="alert">
            <?php echo $message; ?>
        </div>
    </div>
</body>
</html>
