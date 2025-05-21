<?php
session_start();
require '../includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../LoginPage/Userlogin.php');
    exit();
}
$user_id = $_SESSION['user_id'];
// Fetch user info
$stmt = $pdo_makmak1->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo 'User not found.';
    exit();
}
$success = '';
$error = '';
$address = $user['address'] ?? '';
$contact = $user['contact'] ?? '';
// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $contact = trim($_POST['contact'] ?? '');
    // Handle profile picture upload
    $profile_pic = $user['profile_pic'] ?? '';
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newname = 'user_' . $user_id . '_' . time() . '.' . $ext;
            $target = __DIR__ . '/profile_pictures/' . $newname;
            if (!is_dir(__DIR__ . '/profile_pictures')) {
                mkdir(__DIR__ . '/profile_pictures', 0777, true);
            }
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target)) {
                $profile_pic = $newname;
                $stmt = $pdo_makmak1->prepare('UPDATE users SET profile_pic = ? WHERE id = ?');
                $stmt->execute([$profile_pic, $user_id]);
                $success = 'Profile picture updated! ' . $success;
            } else {
                $error = 'Failed to upload profile picture.';
            }
        } else {
            $error = 'Invalid file type. Only JPG, JPEG, PNG allowed.';
        }
    }
    // Update name, address, contact
    $stmt = $pdo_makmak1->prepare('UPDATE users SET firstname = ?, lastname = ?, address = ?, contact = ? WHERE id = ?');
    if ($stmt->execute([$firstname, $lastname, $address, $contact, $user_id])) {
        $success .= ' Profile updated successfully!';
        $user['firstname'] = $firstname;
        $user['lastname'] = $lastname;
        $user['address'] = $address;
        $user['contact'] = $contact;
    } else {
        $error .= ' Failed to update profile.';
    }
    $user['profile_pic'] = $profile_pic;
}
// Set profile picture path
$profilePicPath = (!empty($user['profile_pic']) && file_exists(__DIR__ . '/profile_pictures/' . $user['profile_pic']))
    ? 'profile_pictures/' . $user['profile_pic']
    : '../Images/default_profile.png'; // Use a default image
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
      .profile-picture img { border: 4px solid #3498db; }
      .change-picture-btn { background: #3498db; color: #fff; border: none; border-radius: 50%; width: 38px; height: 38px; position: absolute; bottom: 10px; right: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2em; cursor: pointer; }
      .change-picture-btn:hover { background: #217dbb; }
      .profile-header h2 { font-size: 2.2rem; }
      .profile-section { max-width: 900px; margin: 0 auto; }
      .edit-btn { background: #27ae60; }
      .edit-btn:hover { background: #219150; }
    </style>
</head>
<body>
<div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo"></div>
        <nav class="nav">
            <a href="Dashboard.php"><i class="fas fa-home"></i> Home</a>
            <a href="Booking.php"><i class="fas fa-calendar-check"></i> Book Ice Cream</a>
            <a href="Cart.php"><i class="fas fa-shopping-cart"></i> Your Cart</a>
            <a href="MyOrders.php">
            <i class="fas fa-clipboard-list">
            </i> My Orders
            </a>
            <a href="profile.php" class="active"><i class="fas fa-user"></i> Profile</a>
            <a href="../LoginPage/Userlogin.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    <!-- Main Content -->
    <main class="main">
        <div class="profile-container">
            <div class="profile-header">
                <h2>Your Profile</h2>
                <?php if ($success): ?><div class="notification success"><?= $success ?></div><?php endif; ?>
                <?php if ($error): ?><div class="notification error"><?= $error ?></div><?php endif; ?>
            </div>
            <form method="POST" class="profile-section" enctype="multipart/form-data" autocomplete="off" id="profileForm">
                <div class="profile-picture" style="margin-bottom: 2rem;">
                    <img src="<?= htmlspecialchars($profilePicPath) ?>" alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%;">
                    <label for="profilePicInput" class="change-picture-btn" title="Change Picture"><i class="fas fa-camera"></i></label>
                    <input type="file" id="profilePicInput" name="profile_pic" accept="image/png, image/jpeg, image/jpg" style="display:none;" disabled>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <label>First Name</label>
                        <input type="text" name="firstname" value="<?= htmlspecialchars($user['firstname'] ?? '') ?>" required disabled>
                    </div>
                    <div class="info-item">
                        <label>Last Name</label>
                        <input type="text" name="lastname" value="<?= htmlspecialchars($user['lastname'] ?? '') ?>" required disabled>
                    </div>
                    <div class="info-item">
                        <label>Address</label>
                        <input type="text" name="address" value="<?= htmlspecialchars($address) ?>" required disabled>
                    </div>
                    <div class="info-item">
                        <label>Contact Number</label>
                        <input type="text" name="contact" value="<?= htmlspecialchars($contact) ?>" required disabled>
                    </div>
                </div>
                <button type="button" class="edit-btn" id="editProfileBtn" style="margin-top: 2rem;">Edit Profile</button>
                <button type="submit" class="edit-btn" id="saveChangesBtn" style="margin-top: 2rem; display: none;">Save Changes</button>
            </form>
        </div>
    </main>
</div>
<script>
// Auto-submit form when a new profile picture is selected
const picInput = document.getElementById('profilePicInput');
if (picInput) {
  picInput.addEventListener('change', function() {
    this.form.submit();
  });
}
// Edit button logic
const editBtn = document.getElementById('editProfileBtn');
const saveBtn = document.getElementById('saveChangesBtn');
const form = document.getElementById('profileForm');
if (editBtn && saveBtn && form) {
  editBtn.addEventListener('click', function() {
    form.querySelectorAll('input[type="text"], input[type="file"]').forEach(inp => {
      if (inp.name !== 'username') inp.disabled = false;
    });
    picInput.disabled = false;
    editBtn.style.display = 'none';
    saveBtn.style.display = 'inline-block';
  });
}
</script>
</body>
</html> 