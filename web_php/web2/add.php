<?php
include "db.php";

if (isset($_POST['submit'])) {
    $date = $_POST['date'];
    $title = $_POST['title'];
    $sku = $_POST['sku'];
    $price = $_POST['price'];
    $featured_image = $_POST['featured_image'];
    $categories = $_POST['brandlist']; // Array of selected categories
    $gallery = $_POST['brandlist2']; // Array of selected categories
    $tags = $_POST['brandlist3']; // Array of selected categories



    // Insert into products table
    $sql = "INSERT INTO products (date, title, sku, price, featured_image) VALUES ('$date', '$title','$sku','$price','$featured_image')";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Retrieve the product_id of the newly inserted product
        $product_id = mysqli_insert_id($conn);

        // Insert into product_cat table for each selected category
        foreach ($categories as $cat_id) {
            $sql2 = "INSERT INTO product_cat (product_id, cat_id) VALUES ('$product_id', '$cat_id')";
            mysqli_query($conn, $sql2);
        }

        foreach ($gallery as $gallery_id) {
            $sql2 = "INSERT INTO product_gallery (product_id, gallery_id) VALUES ('$product_id', '$gallery_id')";
            mysqli_query($conn, $sql2);
        }
        foreach ($tags as $tag_id) {
            $sql2 = "INSERT INTO product_tags (product_id, tag_id) VALUES ('$product_id', '$tag_id')";
            mysqli_query($conn, $sql2);
        }

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
                <label for="date">Date:</label>
                <input type="date" id="datePickerId" name="date" />

            </div>
            <div class="form-group mb-2">
                <label for="title">Product name:</label>
                <input onkeyup="lettersOnly(this)" type="text" class="form-control" id="title" name="title">
            </div>
            <div class="form-group mb-2">
                <label for="sku">SKU:</label>
                <input onkeyup="lettersOnly(this)" type="text" class="form-control" id="sku" name="sku">
            </div>
            <div class="form-group mb-2">
                <label for="price">Price:</label>
                <input onkeyup="lettersOnly(this)" type="text" class="form-control" id="price" name="price">
            </div>
            <div class="form-group mb-2">
                <label for="featured_image">Featured image:</label>
                <input onkeyup="lettersOnly(this)" type="text" class="form-control" id="featured_image" name="featured_image">
            </div>
            <div class="form-group mb-2">
                <label for="gallery">Gallery:

                    <select name="brandlist2[]" multiple>
                        <?php
                    $sql = "SELECT * FROM gallery";
                    $result = mysqli_query($conn, $sql);
                    while ($data = mysqli_fetch_array($result)) { ?>
                        <option value="<?php echo $data['id']; ?>">
                            <?php echo $data['name']; ?>
                        </option>
                        <?php } ?>
                    </select>
                </label>

            </div>
            <div class="form-group mb-2">
                <label for="">Categories:
                    <select name="brandlist[]" multiple>
                        <?php
                        $sql = "SELECT * FROM category";
                        $result = mysqli_query($conn, $sql);
                        while ($data = mysqli_fetch_array($result)) { ?>
                            <option value="<?php echo $data['id']; ?>">
                                <?php echo $data['name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </label>

            </div>
            <div class="form-group mb-2">
                <label for="tags">Tags:
                    <select name="brandlist3[]" multiple>
                        <?php
                        $sql = "SELECT * FROM tags";
                        $result = mysqli_query($conn, $sql);
                        while ($data = mysqli_fetch_array($result)) { ?>
                            <option value="<?php echo $data['id']; ?>">
                                <?php echo $data['name']; ?>
                            </option>
                        <?php } ?>
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
        datePickerId.max = new Date().toISOString().split("T")[0];

        function lettersOnly(input) {

            let result = text.replace(/^\s+|\s+$/gm, '');

        }
    </script>

</body>

</html>