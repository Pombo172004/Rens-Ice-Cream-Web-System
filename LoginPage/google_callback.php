<?php

session_start();

require '../includes/db.php'; // Assuming you have your database connection here

// Google OAuth2 Configuration
$clientId = getenv('GOOGLE_CLIENT_ID'); // Read Client ID from environment variable
$clientSecret = getenv('GOOGLE_CLIENT_SECRET'); // Read Client Secret from environment variable
$redirectUri = 'http://localhost/Makmak1/LoginPage/google_callback.php'; // Make sure this matches your Authorized redirect URI

if (isset($_GET['code'])) {
    $authCode = $_GET['code'];

    // Exchange authorization code for access token and ID token
    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $tokenParams = [
        'code' => $authCode,
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri' => $redirectUri,
        'grant_type' => 'authorization_code',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenParams));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tokenResponse = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($tokenResponse, true);

    if (isset($tokenData['access_token'])) {
        $accessToken = $tokenData['access_token'];
        $idToken = $tokenData['id_token'];

        // TODO: Verify the ID token (important for security)

        // Fetch user profile information using the access token
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v3/userinfo'; // Or use Google People API endpoint
        $userInfoCh = curl_init();
        curl_setopt($userInfoCh, CURLOPT_URL, $userInfoUrl);
        curl_setopt($userInfoCh, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        curl_setopt($userInfoCh, CURLOPT_RETURNTRANSFER, true);
        $userInfoResponse = curl_exec($userInfoCh);
        curl_close($userInfoCh);

        $userInfo = json_decode($userInfoResponse, true);

        if (isset($userInfo['email'])) {
            $googleEmail = $userInfo['email'];
            $googleName = $userInfo['name'] ?? ''; // Get name if available

            // TODO: Implement your user login/registration logic here
            // Check if user exists in your database based on $googleEmail
            // If exists, log them in (set session variables)
            // If not exists, create a new user in your database

            // Example: Check if user exists and log them in
            $stmt = $pdo_makmak1->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$googleEmail]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // User exists, log them in
                $_SESSION['user_id'] = $user['id'];
                // You might want to update their name in your database if you got it from Google
                header('Location: ../UserDashboard/Dashboard.php');
                exit();
            } else {
                // User does not exist, you might want to create a new account for them
                // For simplicity, I'll just show an error for now
                // $_SESSION['error'] = "No existing account found for this Google email. Please sign up first.";
                // header('Location: ../LoginPage/Userlogin.php');
                // exit();
                
                // Example of creating a new user:
                $insertStmt = $pdo_makmak1->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                // You'll need to decide how to handle username and password for Google users. Maybe set a dummy password or leave it null if your schema allows.
                // You might also want to add a flag indicating this is a Google-signed-up user.
                $dummyPassword = password_hash(uniqid(), PASSWORD_DEFAULT); // Example: Create a random hashed password
                $insertStmt->execute([$googleEmail, $googleEmail, $dummyPassword]); // Using email as username for simplicity
                $newUserId = $pdo_makmak1->lastInsertId();
                $_SESSION['user_id'] = $newUserId;
                header('Location: ../UserDashboard/Dashboard.php');
                exit();
                
            }

        } else {
            $_SESSION['error'] = "Could not retrieve Google profile information.";
            header('Location: ../LoginPage/Userlogin.php');
            exit();
        }

    } else {
        // Handle error exchanging code for token
        $_SESSION['error'] = "Error during Google token exchange.";
        header('Location: ../LoginPage/Userlogin.php');
        exit();
    }

} else if (isset($_GET['error'])) {
    // Handle errors from Google (e.g., user denied access)
    $error = $_GET['error'];
    $_SESSION['error'] = "Google OAuth2 Error: " . htmlspecialchars($error);
    header('Location: ../LoginPage/Userlogin.php');
    exit();

} else {
    // Should not happen in a normal OAuth2 flow
    $_SESSION['error'] = "Invalid Google OAuth2 request.";
    header('Location: ../LoginPage/Userlogin.login.php'); // Corrected redirect
    exit();
}

?> 