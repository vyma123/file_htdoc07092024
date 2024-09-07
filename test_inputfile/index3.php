<?php
include 'db.php';
require_once 'functions.php';
session_start();

function addProduct($conn, $username, $files, $file)
{
    // Chuyển đổi dữ liệu PHP sang JSON
    $files_json = json_encode($files);

    $sql = "INSERT INTO products (username, files, file) VALUES ('$username', '$files_json', '$file')";
    if (mysqli_query($conn, $sql)) {
        echo "New product added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty(trim($_POST['username']))) {
        $username = htmlspecialchars(trim($_POST['username']));

        $files = isset($_SESSION['files']) ? $_SESSION['files'] : [];
        $file = isset($_SESSION['file']) ? $_SESSION['file'] : '';

        if (!empty($_FILES['files']['name'][0])) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            foreach ($_FILES['files']['name'] as $key => $file_name) {
                $tmp_name = $_FILES['files']['tmp_name'][$key];
                $unique_name = uniqid() . '-' . basename($file_name); // Tạo tên tệp duy nhất
                $file_path = $upload_dir . $unique_name;
                move_uploaded_file($tmp_name, $file_path);
                $files[] = $unique_name;
            }
        }
        if (!empty($_FILES['file']['name'])) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $singleFile_name = uniqid() . '-' . basename($_FILES['file']['name']); // Tạo tên tệp duy nhất
            $singleFile_tmp_name = $_FILES['file']['tmp_name'];
            $singleFile_path = $upload_dir . $singleFile_name;
            move_uploaded_file($singleFile_tmp_name, $singleFile_path);
            $file = $singleFile_name;
        }

        if (!empty($files) || !empty($file)) {
            addProduct($conn, $username, $files, $file);
            unset($_SESSION['files']);
            unset($_SESSION['file']);
            unset($_SESSION['username']);
        } else {
            echo "Please select files to upload.";
        }
    } else {
        echo "Please enter a username.";
        if (!empty($_FILES['files']['name'][0])) {
            unset($_SESSION['files']);
            $files = isset($_SESSION['files']) ? $_SESSION['files'] : [];
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            foreach ($_FILES['files']['name'] as $key => $file_name) {
                $tmp_name = $_FILES['files']['tmp_name'][$key];
                $unique_name = uniqid() . '-' . basename($file_name); // Tạo tên tệp duy nhất
                $file_path = $upload_dir . $unique_name;
                move_uploaded_file($tmp_name, $file_path);
                $files[] = $unique_name;
            }
            $_SESSION['files'] = $files;
        }

        if (!empty($_FILES['file']['name'][0])) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $singleFile_name = uniqid() . '-' . basename($_FILES['file']['name']); // Tạo tên tệp duy nhất
            $singleFile_tmp_name = $_FILES['file']['tmp_name'];
            $singleFile_path = $upload_dir . $singleFile_name;
            move_uploaded_file($singleFile_tmp_name, $singleFile_path);
            $_SESSION['file'] = $singleFile_name;
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
    <title>Hello world</title>
</head>

<body>

    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" name="username" value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>">
        <input type="file" name="file" accept=".png, .jpg, .jpeg, .gif">
        <input type="file" name="files[]" accept=".png, .jpg, .jpeg, .gif" multiple>
        <input type="submit">
    </form>

    <?php
    if (isset($_SESSION['files']) && !empty($_SESSION['files'])) {
        echo "<h3>Uploaded multiple images:</h3>";
        foreach ($_SESSION['files'] as $file) {
            $file_path = 'uploads/' . $file;
            echo "<img src='$file_path' width='150px'>";
        }
    }
    if (isset($_SESSION['file']) && !empty($_SESSION['file'])) {
        $file_path = 'uploads/' . $_SESSION['file'];
        echo "<img src='$file_path' width='150px'>";
    }
    ?>

</body>

</html>