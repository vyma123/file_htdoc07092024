<?php
include "db.php";

if (isset($_POST['submit'])) {
    $gallery = $_POST['gallery'];
    $categories = $_POST['categories'];
    $tags = $_POST['tags'];

    $sql = "
    insert into category ( name) values ('$categories')
    ";
    $result = mysqli_query($conn, $sql);

    $sql2 = "
    insert into gallery ( name) values ('$gallery')
    ";

    $result2 = mysqli_query($conn, $sql2);

    $sql3 = "
    insert into tags ( name) values ('$tags')
    ";

    $result3 = mysqli_query($conn, $sql3);

    if ($result) {
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
            Add property
        </div>
        <form method="post" class="form_add_box">
            
            <div class="form-group mb-2">
                <label for="gallery">Gallery:</label>
                <input type="text" class="form-control" id="gallery" name="gallery">
            </div>
            <div class="form-group mb-2">
                <label for="categories">Categories:</label>
                <input type="text" class="form-control" id="categories" name="categories">
            </div>
            <div class="form-group mb-2">
                <label for="tags">Tags:</label>
                <input type="text" class="form-control" id="tags" name="tags">
            </div>
            <div class="actions">
                <button type="submit" name="submit" class="ui button">OK</button>
            </div>
            <a href="index.php" class="ui button closeModal">Close</a>
        </form>

    </div>

    <script>
        datePickerId.max = new Date().toISOString().split("T")[0];
    </script>
</body>

</html>