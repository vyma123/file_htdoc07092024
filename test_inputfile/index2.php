<?php
session_start();

$svname = "localhost";
$username = "root";
$password = "";
$dbname = "multipleimages";

// Create connection
$conn = mysqli_connect($svname, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to add a product
function addProduct($conn, $username, $files, $singleFile)
{
    $files_json = json_encode($files);
    $sql = "INSERT INTO products (username, files, file) VALUES ('$username', '$files_json', '$singleFile')";
    if (mysqli_query($conn, $sql)) {
        echo "New product added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['username'])) {
        $username = $_POST['username'];

        // If there are files in the session, add them to the files array
        $files = isset($_SESSION['files']) ? $_SESSION['files'] : [];
        $singleFile = isset($_SESSION['singleFile']) ? $_SESSION['singleFile'] : "";

        // Add newly uploaded files to the files array
        if (!empty($_FILES['files']['name'][0])) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            foreach ($_FILES['files']['name'] as $key => $file_name) {
                $tmp_name = $_FILES['files']['tmp_name'][$key];
                $file_path = $upload_dir . basename($file_name);
                move_uploaded_file($tmp_name, $file_path);
                $files[] = $file_name; // Save only the file name
            }
        }

        // Handle the single file upload
        if (!empty($_FILES['singleFile']['name'])) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $singleFile_name = basename($_FILES['singleFile']['name']);
            $singleFile_tmp_name = $_FILES['singleFile']['tmp_name'];
            $singleFile_path = $upload_dir . $singleFile_name;
            move_uploaded_file($singleFile_tmp_name, $singleFile_path);
            $singleFile = $singleFile_name; // Save only the file name
        }

        // Save to database
        if (!empty($files) || !empty($singleFile)) {
            addProduct($conn, $username, $files, $singleFile);
            // Clear session
            unset($_SESSION['files']);
            unset($_SESSION['singleFile']);
            unset($_SESSION['username']);
        } else {
            echo "Please select files to upload.";
        }
    } else {
        echo "Please enter a username.";
        if (!empty($_FILES['files']['name'][0])) {
            unset($_SESSION['files']);
            // Add newly uploaded files to the session
            $files = isset($_SESSION['files']) ? $_SESSION['files'] : [];
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            foreach ($_FILES['files']['name'] as $key => $file_name) {
                $tmp_name = $_FILES['files']['tmp_name'][$key];
                $file_path = $upload_dir . basename($file_name);
                move_uploaded_file($tmp_name, $file_path);
                $files[] = $file_name; // Save only the file name
            }

            $_SESSION['files'] = $files;

        }

        if (!empty($_FILES['singleFile']['name'])) {
            // Handle the single file upload to the session
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $singleFile_name = basename($_FILES['singleFile']['name']);
            $singleFile_tmp_name = $_FILES['singleFile']['tmp_name'];
            $singleFile_path = $upload_dir . $singleFile_name;
            move_uploaded_file($singleFile_tmp_name, $singleFile_path);
            $_SESSION['singleFile'] = $singleFile_name; // Save only the file name
        }

        $_SESSION['username'] = $_POST['username'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input 2</title>
</head>

<body>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" name="username" id="" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>">
        <input type="file" name="singleFile" accept=".jpg, .png, .jpeg">
        <input type="file" name="files[]" accept=".jpg, .png, .jpeg" multiple>
        <input type="submit">
    </form>

    <?php
    if (isset($_SESSION['files']) && !empty($_SESSION['files'])) {
        echo "<h3>Uploaded Multiple Images:</h3>";
        foreach ($_SESSION['files'] as $file) {
            $file_path = 'uploads/' . $file;
            echo "<img src='$file_path' alt='uploaded image' style='width: 100px; height: 100px;'><br>";
        }
    }

    if (isset($_SESSION['singleFile']) && !empty($_SESSION['singleFile'])) {
        echo "<h3>Uploaded Single Image:</h3>";
        $file_path = 'uploads/' . $_SESSION['singleFile'];
        echo "<img src='$file_path' alt='uploaded image' style='width: 100px; height: 100px;'><br>";
    }
    ?>
</body>
</html>

