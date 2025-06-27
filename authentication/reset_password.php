<?php
$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../connection.php';

    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    if (empty($token) || empty($newPassword)) {
        $errorMessage = "Token and new password are required.";
    } else {
        $stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $errorMessage = "Invalid or expired token.";
        } else {
            $row = $result->fetch_assoc();
            $email = $row['email'];
            $expiresAt = $row['expires_at'];

            if (strtotime($expiresAt) < time()) {
                $errorMessage = "Token has expired.";
            } else {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $updateStmt->bind_param("ss", $hashedPassword, $email);
                if ($updateStmt->execute()) {
                    $deleteStmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
                    $deleteStmt->bind_param("s", $token);
                    $deleteStmt->execute();

                    $successMessage = "Password has been reset successfully.";
                } else {
                    $errorMessage = "Failed to update password.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f4f4;
        }
        .container {
            max-width: 450px;
            margin-top: 80px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .checklist {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px 15px;
            margin-top: 15px;
        }
        .checklist i {
            width: 18px;
        }
        /* Custom Modal */
        .modal-custom {
            display: none;
            position: fixed;
            z-index: 9999;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.4);
            justify-content: center;
            align-items: center;
        }
        .modal-content-custom {
            background: white;
            padding: 25px 30px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
        .modal-content-custom h4 {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <h3 class="text-center mb-4">Reset Your Password</h3>
    <form method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">

        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <div class="input-group">
                <input type="password" class="form-control" name="new_password" id="new_password" required oninput="validatePassword()">
                <span class="input-group-text" onclick="togglePassword()" style="cursor:pointer;">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                </span>
            </div>
        </div>

        <div class="checklist" id="passwordChecklist">
            <div><i class="fas fa-times text-danger" id="upperCheck"></i> One uppercase letter</div>
            <div><i class="fas fa-times text-danger" id="lowerCheck"></i> One lowercase letter</div>
            <div><i class="fas fa-times text-danger" id="numberCheck"></i> One number</div>
            <div><i class="fas fa-times text-danger" id="specialCheck"></i> One special character</div>
            <div><i class="fas fa-times text-danger" id="lengthCheck"></i> At least 8 characters</div>
        </div>

        <button type="submit" class="btn btn-success mt-4 w-100">Reset Password</button>
    </form>
</div>

<!-- Custom Modal -->
<div class="modal-custom" id="feedbackModal">
    <div class="modal-content-custom">
        <h4 id="modalTitle"></h4>
        <p id="modalMessage"></p>
        <button class="btn btn-primary mt-3" onclick="closeModal()">OK</button>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('new_password');
    const icon = document.getElementById('toggleIcon');
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
}

function validatePassword() {
    const password = document.getElementById('new_password').value;
    const checks = {
        upperCheck: /[A-Z]/.test(password),
        lowerCheck: /[a-z]/.test(password),
        numberCheck: /[0-9]/.test(password),
        specialCheck: /[^A-Za-z0-9]/.test(password),
        lengthCheck: password.length >= 8
    };

    for (const [id, passed] of Object.entries(checks)) {
        const icon = document.getElementById(id);
        icon.classList.remove('fa-times', 'text-danger', 'fa-check', 'text-success');
        if (passed) {
            icon.classList.add('fa-check', 'text-success');
        } else {
            icon.classList.add('fa-times', 'text-danger');
        }
    }
}

function showModal(title, message) {
    document.getElementById("modalTitle").innerText = title;
    document.getElementById("modalMessage").innerText = message;
    document.getElementById("feedbackModal").style.display = "flex";
}

function closeModal() {
    document.getElementById("feedbackModal").style.display = "none";
}

// Show modal if PHP sets a message
<?php if (!empty($successMessage)) : ?>
    showModal("Success", "<?php echo addslashes($successMessage); ?>");
<?php elseif (!empty($errorMessage)) : ?>
    showModal("Error", "<?php echo addslashes($errorMessage); ?>");
<?php endif; ?>
</script>
</body>
</html>
