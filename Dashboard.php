<?php
session_start();
include "connection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM documents WHERE user_id='$user_id'");
?>

<!DOCTYPE html>
<html>
<head>
<title>Vision Dashboard</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Orbitron, sans-serif;
}

body{
    background:#020617;
    color:#0ff;
    min-height:100vh;
    overflow:hidden;
}

/* animated scan background */
body::before{
    content:"";
    position:absolute;
    width:100%;
    height:100%;
    background:repeating-linear-gradient(
        to bottom,
        rgba(0,255,255,0.05),
        rgba(0,255,255,0.05) 2px,
        transparent 2px,
        transparent 6px
    );
    animation:scan 6s linear infinite;
    z-index:-1;
}

@keyframes scan{
    from{transform:translateY(-100%);}
    to{transform:translateY(100%);}
}

/* HEADER */
header{
    display:flex;
    justify-content:space-between;
    padding:20px;
    border-bottom:1px solid rgba(0,255,255,0.2);
}

header h2{
    text-shadow:0 0 10px #0ff;
}

header a{
    color:red;
    text-decoration:none;
}

/* MAIN CONTAINER */
.container{
    padding:30px;
}

/* CARDS */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(200px,1fr));
    gap:20px;
}

.card{
    padding:20px;
    border-radius:15px;
    border:1px solid rgba(0,255,255,0.3);
    backdrop-filter:blur(10px);
    box-shadow:0 0 20px rgba(0,255,255,0.2);
    text-align:center;
    transition:0.3s;
}

.card:hover{
    transform:translateY(-5px);
    box-shadow:0 0 30px #0ff;
}

/* SCAN BOX */
.scan-box{
    margin-top:40px;
    padding:25px;
    border:1px solid rgba(0,255,255,0.3);
    border-radius:20px;
    text-align:center;
    position:relative;
}

.scan-box::after{
    content:"";
    position:absolute;
    width:100%;
    height:2px;
    background:#0ff;
    top:0;
    animation:scanLine 3s infinite;
}

@keyframes scanLine{
    0%{top:0;}
    100%{top:100%;}
}

input{
    padding:10px;
    background:#000;
    border:1px solid #0ff;
    color:#0ff;
    margin-top:10px;
}

button{
    margin-top:10px;
    padding:8px 15px;
    background:#0ff;
    border:none;
    cursor:pointer;
    border-radius:10px;
    font-size:12px;
}

button:hover{
    box-shadow:0 0 10px #0ff;
}

/* TABLE */
.table{
    margin-top:40px;
    width:100%;
    border-collapse:collapse;
}

.table th,.table td{
    border:1px solid rgba(0,255,255,0.3);
    padding:10px;
    text-align:center;
}

.table th{
    background:rgba(0,255,255,0.1);
}

/* LINKS */
.view-link{
    color:#00ffcc;
    font-weight:bold;
    text-decoration:none;
    margin-right:10px;
}

.download-link{
    color:#0ff;
    font-weight:bold;
    text-decoration:none;
}

.view-link:hover,
.download-link:hover{
    text-decoration:underline;
}
</style>

</head>

<body>

<header>
    <h2>👁 Welcome, <?php echo $_SESSION['user']; ?></h2>
    <a href="login.php">Logout</a>
</header>

<div class="container">

    <!-- CARDS -->
    <div class="cards">
        <div class="card">
            <h2><?php echo $result->num_rows; ?></h2>
            <p>Scanned Files</p>
        </div>

        <div class="card">
            <h2>ACTIVE</h2>
            <p>Status</p>
        </div>

        <div class="card">
            <h2>OCR</h2>
            <p>Enabled</p>
        </div>
    </div>

    <!-- SCAN FORM -->  
    <div class="scan-box">
        <h2>📡 Upload & Scan</h2>

        <form action="scan.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <br>
            <button name="scan">Start Scan</button>
        </form>
    </div>

    <!-- TABLE -->
    <table class="table">
        <tr>
            <th>ID</th>
            <th>File</th>
            <th>Status</th>
        </tr>

        <?php while($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['file_name']; ?></td>

            <td>
                <!-- VIEW -->
                <a class="view-link" href="view.php?id=<?php echo $row['id']; ?>">
                    View
                </a>

                <!-- DOWNLOAD -->
                <a class="download-link" 
                   href="<?php echo $row['file_path']; ?>" 
                   download>
                    Download
                </a>
            </td>

        </tr>
        <?php } ?>
    </table>

</div>

</body>
</html>