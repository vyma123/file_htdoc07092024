<?php

function addProduct($conn, $username, $files, $file)
{
    // Chuyển đổi dữ liệu PHP sang JSON
    $files_json = json_encode($files);

    $sql = $conn->prepare("INSERT INTO products (username, files, file) VALUES (?,?,?)");
    $sql->bind_param("sss", $username, $files_json, $file);
    

    if ($sql->execute()) {
        echo "New product added successfully";
        
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error();
    }
    $sql->close();
    $conn->close();
}
 ?>