<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<!-- HEAD-->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- This line of code is to look good of all devices-->
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css"><style>
        /* Modal or the Pop up form */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 30px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
        }

        .modal-content h3 {
            margin-bottom: 15px;
        }

        .modal-content input {
            width: 80%;
            padding: 10px;
            margin-bottom: 15px;
        }

        .modal-content button {
            padding: 10px 20px;
            background-color: #6c42f5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal-content button:hover {
            background-color: #5632cc;
        }
    </style>
</head>

<!-- BODY-->
<body>

        <!-- Right Side of the Login container -->
        <div class="right-panel">
            <h2>Register!</h2>

                    <?php if (isset($_SESSION['error'])): ?>
                    <div class = "alert alert-danger" role = "after">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                    <?php endif; ?>
                    
            <form action="signupValidate.php" method="POST">

                <input class="form-control" type="text" placeholder="Firstname" name="firstname" required>
                <input class="form-control" type="text" placeholder="Lastname" name="lastname" required>
                <input class="form-control" type="text" placeholder="Username" name="username" required>
                <input class="form-control" type="email" placeholder="Email" name="email" require>

                <input class="form-control" type="password" placeholder="Password" name="Password" required>
                <input class="form-control" type="password" placeholder="Re-enter Password" name="confirmPassword" required>
                <button type="submit" class="btn">Signup</button>
                <p>Already have an Account? <a href="Userlogin.php">Login</a></p>
        </form>
        
        </div>

</body>
</html>