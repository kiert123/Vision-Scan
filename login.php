<?php
session_start();
include "connection.php";

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {

            $_SESSION['user'] = $row['full_name'];
            $_SESSION['user_id'] = $row['id'];

            header("Location: dashboard.php");
            exit();

        } else {
            $error = "Wrong Password";
        }
    } else {
        $error = "User Not Found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>

<!-- ✅ SAME FONT AS REGISTER -->
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

/* INPUT FIELDS */
.container input{
    width:100%;
    padding:12px;
    margin:10px 0;
    border-radius:10px;
    border:1px solid #0ff;
    background:#000;
    color:#0ff;
    outline:none;
    font-size:14px;
}

/* INPUT FOCUS EFFECT */
.container input:focus{
    box-shadow:0 0 10px #0ff;
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
    text-align:center;
    margin-top:1rem;
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
    <h2>VISION LOGIN</h2>

    <!-- ✅ ERROR DISPLAY -->
    <?php if (!empty($error)) { ?>
        <div class="error"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="login">Login</button>
    </form>

    <div class="link">
        <a href="rigester.php">Create Identity</a>
    </div>
</div>

</body>
</html>