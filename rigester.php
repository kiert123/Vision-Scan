<?php
session_start();
include "connection.php";

if (isset($_POST['register'])) {

    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 🔒 HASH PASSWORD
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // ✅ CHECK IF EMAIL EXISTS
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");

    if ($check->num_rows > 0) {
        $error = "Email already registered!";
    } else {

        // ✅ INSERT USER
        $sql = "INSERT INTO users (full_name, email, password)
                VALUES ('$name', '$email', '$hashed_password')";

        if ($conn->query($sql)) {

            // ✅ REDIRECT TO LOGIN
            header("Location: login.php");
            exit();

        } else {
            $error = "Registration failed!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register</title>

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600&display=swap" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: Orbitron, Arial, sans-serif;
}

body{
    background:#01040f;
    color:#0ff;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

/* MAIN CONTAINER */
.container{
    width:350px;
    padding:30px;
    border-radius:20px;
    border:1px solid rgba(0,255,255,0.3);
    box-shadow:0 0 40px rgba(0,255,255,0.2);
    text-align:center;
    background:rgba(0,0,0,0.6);
}

/* TITLE */
.container h2{
    margin-bottom:20px;
    font-size:24px;
    letter-spacing:2px;
}

/* INPUT */
.container input{
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:10px;
    border:1px solid #0ff;
    background:#000;
    color:#0ff;
    outline:none;
}

/* BUTTON */
.container button{
    width:100%;
    padding:12px;
    margin-top:15px;
    background:#0ff;
    color:#000;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
    transition:0.3s;
}

.container button:hover{
    background:#00cccc;
    box-shadow:0 0 15px #0ff;
}

/* LINK */
.link{
    margin-top:15px;
}

.link a{
    color:#0ff;
    text-decoration:none;
}

/* ERROR */
.error{
    color:red;
    margin-top:10px;
}
</style>

</head>

<body>

<div class="container">
    <h2>Create Account</h2>

    <!-- ERROR MESSAGE -->
    <?php if (!empty($error)) { ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name="register">Sign Up</button>
    </form>

    <div class="link">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>

</body>
</html>