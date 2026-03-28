<?php
session_start();
include "connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("No file selected.");
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

/* ==========================
   GET DOCUMENT
========================== */
$result = $conn->query("SELECT * FROM documents WHERE id='$id' AND user_id='$user_id'");

if ($result->num_rows == 0) {
    die("File not found.");
}

$row = $result->fetch_assoc();

/* ==========================
   UPDATE TEXT
========================== */
if (isset($_POST['update'])) {

    $new_text = $_POST['text'];

    $conn->query("UPDATE documents 
                  SET extracted_text='$new_text', status='Updated' 
                  WHERE id='$id' AND user_id='$user_id'");

    echo "<script>alert('Updated Successfully!'); window.location='dashboard.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>View Document</title>

<style>
body{
    background:#01040f;
    color:#0ff;
    font-family:Orbitron;
    text-align:center;
}

.container{
    width:90%;
    max-width:800px;
    margin:40px auto;
    padding:30px;
    border:1px solid rgba(0,255,255,0.3);
    border-radius:20px;
    box-shadow:0 0 20px rgba(0,255,255,0.2);
}

textarea{
    width:100%;
    height:300px;
    background:#000;
    color:#0ff;
    border:1px solid #0ff;
    padding:10px;
}

button{
    margin-top:15px;
    padding:10px 20px;
    background:#0ff;
    border:none;
    border-radius:10px;
    cursor:pointer;
}

button:hover{
    box-shadow:0 0 10px #0ff;
}

.back{
    display:inline-block;
    margin-top:15px;
    color:#0ff;
    text-decoration:none;
}
</style>

</head>

<body>

<div class="container">

<h2>📄 <?php echo $row['file_name']; ?></h2>

<p>Status: <b><?php echo $row['status']; ?></b></p>

<form method="POST">

<textarea name="text"><?php echo $row['extracted_text']; ?></textarea>

<br>

<button name="update">💾 Save Changes</button>

</form>

<a href="dashboard.php" class="back">⬅ Back to Dashboard</a>

</div>

</body>
</html>