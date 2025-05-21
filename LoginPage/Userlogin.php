<?php

session_start();

?>

<!-- LOGIN PAGE-->
<!DOCTYPE html>
<html lang="en">

<!-- HEAD-->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  <!-- This line of code is to look good of all devices-->
    <title>Login Form</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Google reCAPTCHA API -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>


<!-- BODY -->
<body>

        <!-- Right Side of the Login container -->
        <div class="right-panel">
            <h2>Welcome!</h2>

            <div class="card-body">

                        <?php if (isset($_SESSION['error'])): ?>
                        <div class= "alert alert-danger" role="alert">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>

                        <?php endif; ?>
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success" role="alert" >
                                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>
                    
                <form action="loginValidate.php" method="POST" >
                    <input required class="form-control" type="text" placeholder="username" name="username">
                    <br>
                    <input required class="form-control" type="password" placeholder="Password" name="password"> <br>
                    
                    <!-- Add reCAPTCHA div here -->
                    <div class="g-recaptcha" data-sitekey="6Lcr9kIrAAAAACN2oe4wDpYtYBiirQl9zFCBPht4
">

                    </div>
                    
                    <button class="btn btn-primary" type="submit"> Login</button>
                </form>
                    <p>Don't have an account? <a href="Usersignin.php"> Signup</a></p>
                    <p>Forgot password? <a href="forgotpassword.php"> Click Here!</a></p>

                    <!-- Google Login Link -->
                    <p>Or sign in with: <a href="https://accounts.google.com/o/oauth2/auth?client_id=593939635447-pl2hb3i8p0qnfan6fplv1is36enc4iao.apps.googleusercontent.com&redirect_uri=http://localhost/Makmak1/LoginPage/google_callback.php&response_type=code&scope=openid%20email%20profile">Google</a></p>
            </div>
        </div>

</body>
</html>
