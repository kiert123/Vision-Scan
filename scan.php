<?php
session_start();
include "connection.php";

/* ==========================
   ✅ CHECK LOGIN
========================== */
if (!isset($_SESSION['user']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$filepath = "";
$filename = "";
$extracted_text = "";

/* ==========================
   📄 FUNCTION: PDF → TEXT (FIXED)
========================== */
function extractPDFText($file) {

    $text = "";
    $output = "pdf_" . time() . ".txt";

    // ✅ FULL PATH FIX
    $cmd = '"C:\\xampp\\php\\pdftotext.exe" ' . escapeshellarg($file) . ' ' . $output;

    exec($cmd);

    if (file_exists($output)) {
        $text = file_get_contents($output);
        unlink($output);
    } else {
        $text = "(⚠ Cannot extract PDF text. Check pdftotext setup)";
    }

    return $text;
}

/* ==========================
   ✅ UPLOAD + OCR
========================== */
if (isset($_POST['scan'])) {

    if (!isset($_FILES['file']) || $_FILES['file']['error'] != 0) {
        die("File upload error.");
    }

    $file = $_FILES['file'];
    $filename = $file['name'];
    $tmpname = $file['tmp_name'];

    $upload_dir = "uploads/";

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $clean_name = preg_replace("/[^a-zA-Z0-9.]/", "_", $filename);
    $newname = time() . "_" . $clean_name;
    $filepath = $upload_dir . $newname;

    move_uploaded_file($tmpname, $filepath);

    $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

    // 🖼 IMAGE OCR
    if (in_array($ext, ['jpg','jpeg','png'])) {

        $tesseract = '"C:\\Program Files\\Tesseract-OCR\\tesseract.exe"';
        $output = "ocr_" . time();

        exec($tesseract . " " . escapeshellarg($filepath) . " " . escapeshellarg($output));

        if (file_exists("$output.txt")) {
            $extracted_text = file_get_contents("$output.txt");
            unlink("$output.txt");
        } else {
            $extracted_text = "(No text detected)";
        }

    }
    // 📄 PDF → TEXT
    elseif ($ext == 'pdf') {
        $extracted_text = extractPDFText($filepath);
    }
    else {
        $extracted_text = "(Unsupported file)";
    }
}

/* ==========================
   🎨 IMAGE EDIT
========================== */
if (isset($_POST['edit_image'])) {

    $filepath = $_POST['filepath'];
    $action   = $_POST['action'];

    $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

    if (function_exists('imagecreatefromjpeg') && in_array($ext, ['jpg','jpeg','png'])) {

        $img = ($ext == "png") 
            ? imagecreatefrompng($filepath) 
            : imagecreatefromjpeg($filepath);

        if ($action == "rotate") {
            $img = imagerotate($img, 90, 0);
        }

        if ($action == "gray") {
            imagefilter($img, IMG_FILTER_GRAYSCALE);
        }

        if ($action == "color") {
            $r = intval($_POST['r']) - 100;
            $g = intval($_POST['g']) - 100;
            $b = intval($_POST['b']) - 100;

            imagefilter($img, IMG_FILTER_COLORIZE, $r, $g, $b);
        }

        ($ext == "png") 
            ? imagepng($img, $filepath) 
            : imagejpeg($img, $filepath, 100);

        imagedestroy($img);
    }

    $extracted_text = $_POST['text'];
}

/* ==========================
   💾 SAVE
========================== */
if (isset($_POST['save'])) {

    $filepath = $_POST['filepath'];
    $filename = $_POST['filename'];
    $text = $_POST['text'];

    $sql = "INSERT INTO documents (user_id, file_name, file_path, extracted_text, status)
            VALUES ('$user_id', '$filename', '$filepath', '$text', 'Processed')";

    if ($conn->query($sql)) {
        echo "<script>alert('Saved!'); window.location='dashboard.php';</script>";
        exit();
    } else {
        echo "Database Error";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Vision Scan</title>

<style>
body{background:#01040f;color:#0ff;text-align:center;font-family:Arial;}
.container{
    width:90%;max-width:900px;margin:40px auto;padding:30px;
    border-radius:20px;border:1px solid rgba(0,255,255,0.3);
}
img{max-width:400px;border:2px solid #0ff;margin:20px;}
textarea{width:100%;height:250px;background:#000;color:#0ff;border:1px solid #0ff;padding:10px;}
button{margin:10px;padding:10px;background:#0ff;border:none;border-radius:10px;}
</style>
</head>

<body>

<div class="container">
<h1>👁 Scan Result</h1>

<?php if (!empty($filepath)) { ?>
<?php $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION)); ?>

<!-- IMAGE -->
<?php if (in_array($ext, ['jpg','jpeg','png'])) { ?>

<img src="<?php echo $filepath . '?t=' . time(); ?>">

<form method="POST">
<input type="hidden" name="filepath" value="<?php echo $filepath; ?>">
<input type="hidden" name="text" value="<?php echo $extracted_text; ?>">

<button name="edit_image" onclick="this.form.action.value='rotate'">Rotate</button>
<button name="edit_image" onclick="this.form.action.value='gray'">Grayscale</button>

<br>

<input type="color" id="colorPicker">
<input type="hidden" name="r">
<input type="hidden" name="g">
<input type="hidden" name="b">

<button name="edit_image"
onclick="
let c=document.getElementById('colorPicker').value;
this.form.r.value=parseInt(c.substr(1,2),16);
this.form.g.value=parseInt(c.substr(3,2),16);
this.form.b.value=parseInt(c.substr(5,2),16);
this.form.action.value='color';
">Apply Color</button>

<input type="hidden" name="action">
</form>

<!-- PDF -->
<?php } elseif ($ext == 'pdf') { ?>

<p style="color:yellow;">📄 PDF converted to editable text below</p>

<?php } ?>

<h3>✏ Edit Content</h3>

<form method="POST">
<textarea name="text"><?php echo $extracted_text; ?></textarea>

<input type="hidden" name="filepath" value="<?php echo $filepath; ?>">
<input type="hidden" name="filename" value="<?php echo $filename; ?>">

<button name="save">💾 Save</button>
</form>

<?php } ?>

</div>
</body>
</html>