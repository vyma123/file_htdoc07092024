<?php
include 'db.php';
session_start();

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Initialize image and multiple images paths
$imagePath = '';
$multipleImagePaths = [];

// Check if form is submitted
if (isset($_POST['submit'])) {
    // Check if username is provided
    if (empty($_POST['username'])) {
        echo "Vui lòng nhập tên người dùng!";

        // Handle single image upload
        if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] == 0) {
            $imagePath = $uploadDir . basename($_FILES['imageFile']['name']);
            $imageName = basename($_FILES['imageFile']['name']);

            if (!move_uploaded_file($_FILES['imageFile']['tmp_name'], $imagePath)) {
                echo "Lỗi khi tải lên ảnh chính.";
                exit;
            }
        } else {
            $imageName = isset($_POST['existingImage']) ? $_POST['existingImage'] : '';
        }

        // Handle multiple images upload
        if (isset($_FILES['multiple_images']) && count($_FILES['multiple_images']['name']) > 0) {
            foreach ($_FILES['multiple_images']['name'] as $key => $fileName) {
                if ($_FILES['multiple_images']['error'][$key] == 0) {
                    $tempFilePath = $_FILES['multiple_images']['tmp_name'][$key];
                    $newFilePath = $uploadDir . basename($fileName);
                    $newFileName = basename($fileName);

                    if (move_uploaded_file($tempFilePath, $newFilePath)) {
                        $multipleImagePaths[] = $newFileName;
                    } else {
                        echo "Lỗi khi tải lên ảnh gallery.";
                        exit;
                    }
                }
            }
        } else {
            // Retrieve existing multiple image paths from hidden field
            $multipleImagePaths = isset($_POST['existingMultipleImages']) ? json_decode($_POST['existingMultipleImages'], true) : [];
        }
    } else {
        // Username is provided
        $username = $_POST['username'];

        // Use existing image paths if they exist
        $imageName = isset($_POST['existingImage']) ? $_POST['existingImage'] : '';
        $multipleImagePaths = isset($_POST['existingMultipleImages']) ? json_decode($_POST['existingMultipleImages'], true) : [];



        // Handle single image upload
        if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] == 0) {
            $imagePath = $uploadDir . basename($_FILES['imageFile']['name']);
            $imageName = basename($_FILES['imageFile']['name']);

            if (!move_uploaded_file($_FILES['imageFile']['tmp_name'], $imagePath)) {
                echo "Lỗi khi tải lên ảnh chính.";
                exit;
            }
        }

        // Handle multiple images upload
        if (isset($_FILES['multiple_images']) && count($_FILES['multiple_images']['name']) > 0) {
            foreach ($_FILES['multiple_images']['name'] as $key => $fileName) {
                if ($_FILES['multiple_images']['error'][$key] == 0) {
                    $tempFilePath = $_FILES['multiple_images']['tmp_name'][$key];
                    $newFilePath = $uploadDir . basename($fileName);
                    $newFileName = basename($fileName);

                    if (move_uploaded_file($tempFilePath, $newFilePath)) {
                        $multipleImagePaths[] = $newFileName;
                    } else {
                        echo "Lỗi khi tải lên ảnh gallery.";
                        exit;
                    }
                }
            }
        }

        // Convert the gallery image paths to JSON
        $newFileName = json_encode($multipleImagePaths);    

        // Save the user data and images to the database
        $stmt = $conn->prepare("INSERT INTO product (username, img, multiple_images) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $imageName, $newFileName);

        if ($stmt->execute()) {
            echo "Lưu người dùng: $username với ảnh chính: $imagePath và ảnh gallery vào cơ sở dữ liệu thành công.";
        } else {
            echo "Lỗi khi lưu vào cơ sở dữ liệu.";
        }

        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<body>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" placeholder="user name" name="username">
        <br><br>
        <input accept=".png, .jpg, .jpeg" type="file" name="imageFile" id="imageFile">
        <br><br>
        <input accept=".png, .jpg, .jpeg" type="file" name="multiple_images[]" id="multiple_images" multiple>
        <br><br>
        <!-- Hidden fields to store existing images -->
        <input type="hidden" name="existingImage" value="<?php echo isset($imageName) ? $imageName : ''; ?>">
        <?php if (isset($imageName) && !empty($imageName)) { ?>
            <p name="existingImage"><?php echo $imageName; ?></p>
        <?php } ?>

        <input type="hidden" name="existingMultipleImages" value='<?php echo isset($multipleImagePaths) ? json_encode($multipleImagePaths) : '[]'; ?>'>
        <?php if (isset($multipleImagePaths) && !empty($multipleImagePaths)) { ?>
            <p name="existingMultipleImages"><?php echo json_encode($multipleImagePaths); ?></p>
        <?php } ?>

        <input type="submit" name="submit" value="Submit">
    </form>
</body>

</html>