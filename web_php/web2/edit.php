<?php
include 'db.php';


$id = $_GET['editid'];
$sql = "Select * from `products` where ID = '$id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$date = $row['date'];
$title = $row['title'];
$sku = $row['sku'];
$price = $row['price'];
$featured_image = $row['featured_image'];


$sql = "SELECT cat_id FROM product_cat WHERE product_id = '$id'";
$result = mysqli_query($conn, $sql);
$current_categories = [];
while ($data = mysqli_fetch_assoc($result)) {
    $current_categories[] = $data['cat_id'];
}

$sql = "SELECT gallery_id FROM product_gallery WHERE product_id = '$id'";
$result = mysqli_query($conn, $sql);
$current_gallery = [];
while ($data = mysqli_fetch_assoc($result)) {
    $current_gallery[] = $data['gallery_id'];
}

$sql = "SELECT tag_id FROM product_tags WHERE product_id = '$id'";
$result = mysqli_query($conn, $sql);
$current_tag = [];
while ($data = mysqli_fetch_assoc($result)) {
    $current_tag[] = $data['tag_id'];
}

if (isset($_POST['submit'])) {
    
    $gallery = $_POST['gallery'];
    $categories = $_POST['categories'];
    $tags = $_POST['tags'];

    // Update the product
    $sql = "UPDATE products SET date='$date', title='$title', sku='$sku', price='$price', featured_image='$featured_image' WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Clear existing categories for the product
        $sql = "DELETE FROM product_cat WHERE product_id = '$id'";
        mysqli_query($conn, $sql);

        // Insert the new categories
        foreach ($categories as $cat_id) {
            $sql = "INSERT INTO product_cat (product_id, cat_id) VALUES ('$id', '$cat_id')";
            mysqli_query($conn, $sql);
        }

        $sql2 = "DELETE FROM product_gallery WHERE product_id = '$id'";
        mysqli_query($conn, $sql2);

        // Insert the new categories
        foreach ($gallery as $gallery_id) {
            $sql2 = "INSERT INTO product_gallery (product_id, gallery_id) VALUES ('$id', '$gallery_id')";
            mysqli_query($conn, $sql2);
        }

        $sql3 = "DELETE FROM product_tags WHERE product_id = '$id'";
        mysqli_query($conn, $sql3);

        // Insert the new categories
        foreach ($tags as $tag_id) {
            $sql3 = "INSERT INTO product_tags (product_id, tag_id) VALUES ('$id', '$tag_id')";
            mysqli_query($conn, $sql3);
        }

        echo "Update successfully";
        header('Location: index.php');
    } else {
        die(mysqli_error($conn));
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.5.0/semantic.min.css" integrity="sha512-KXol4x3sVoO+8ZsWPFI/r5KBVB/ssCGB5tsv2nVOKwLg33wTFP3fmnXa47FdSVIshVTgsYk/1734xSk9aFIa4A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body>
    <div class="ui active modal" id="add_box_modal">
        <i class="close icon"></i>
        <div class="header">
            Add product
        </div>
        <form method="post" class="form_add_box">
            <div class="form-group mb-2">
                <label for="Date">Date:</label>
                <input type="date" id="datePicker" value="<?php echo $date ?>" name="Date" />

            </div>
            <div class="form-group mb-2">
                <label for="Title">Product name:</label>
                <input type="text" class="form-control" id="Title" name="Title" value="<?php echo $title ?>">
            </div>
            <div class="form-group mb-2">
                <label for="SKU">SKU:</label>
                <input type="text" class="form-control" id="SKU" name="SKU" value="<?php echo $sku ?>">
            </div>
            <div class="form-group mb-2">
                <label for="Price">Price:</label>
                <input type="text" class="form-control" id="Price" name="Price" value="<?php echo $price ?>">
            </div>
            <div class="form-group mb-2">
                <label for="Featured_image">Featured image:</label>
                <input type="text" class="form-control" id="Featured_image" name="Featured_image" value="<?php echo $featured_image ?>">
            </div>
            <div class="form-group mb-2">
                <label for="Gallery">Gallery:
                   
                    <select name="gallery[]" id="gallery" multiple>
                        <?php
                        
                        $sql = "SELECT * FROM gallery";
                        $result = mysqli_query($conn, $sql);
                        while ($data = mysqli_fetch_assoc($result)) {
                            $selected = in_array($data['id'], $current_gallery) ? 'selected' : '';

                                echo "<option value='{$data['id']}' $selected>{$data['name']}</option>";
                            
                        }
                        ?>
                    </select>
                </label>
            </div>
            <div class="form-group mb-2">
                <label for="Categories">Categories:

                    <select name="categories[]" id="categories" multiple>
                        <?php
                        $sql = "SELECT * FROM category";
                        $result = mysqli_query($conn, $sql);
                        while ($data = mysqli_fetch_assoc($result)) {
                            $selected = in_array($data['id'], $current_categories) ? 'selected' : '';
                            echo "<option value='{$data['id']}' $selected>{$data['name']}</option>";
                        }
                        ?>
                    </select>
                </label>
            </div>
            <div class="form-group mb-2">
                <label for="tags">Tags:
                    <select name="tags[]" id="tags" multiple>
                        <?php
                        $sql = "SELECT * FROM tags";
                        $result = mysqli_query($conn, $sql);
                        while ($data = mysqli_fetch_assoc($result)) {
                            $selected = in_array($data['id'], $current_tag) ? 'selected' : '';
                            echo "<option value='{$data['id']}' $selected>{$data['name']}</option>";
                        }
                        ?>
                    </select>
                </label>
               
            </div>
            <div class="actions">
                <button type="submit" name="submit" class="ui button">OK</button>
            </div>
            <a href="index.php" class="ui button closeModal">Close</a>
        </form>

    </div>


    <script>
        document.getElementById('datePicker').valueAsDate = new Date();
    </script>

</body>

</html>