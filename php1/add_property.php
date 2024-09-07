<?php
include 'db.php';


if(isset($_POST['add'])) {
    foreach ($_POST as $key => $value) {
        if(!empty(trim($value))) {
            $name = test_input($value);
            $type = $key;
            $result = $conn->prepare("select id from property where name_ = ? and type_ = ?");
            $result->bind_param("ss", $name,$type);
            $result->execute();
            $result->store_result();
            if($result->num_rows() > 0 ){
                print_r($result);
                echo 'exist';
            }else{
               $result = $conn->prepare("insert into property (name_, type_) values (?,?)");
               $result->bind_param("ss", $name, $type);
               if($result->execute()){
                echo 'successfully';
               }else{
                echo 'failed';
               }
                $result->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property</title>

    <link rel="stylesheet" href="style.css">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- link semantic ui -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.5.0/semantic.min.css" integrity="sha512-KXol4x3sVoO+8ZsWPFI/r5KBVB/ssCGB5tsv2nVOKwLg33wTFP3fmnXa47FdSVIshVTgsYk/1734xSk9aFIa4A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <h1 class="add_property">Add Property</h1>
    <form action="" method="post">
        <div class="container_property">
            <div class="ui input">
                <input name="category" type="text" placeholder="Category...">
            </div>
            <div class="ui input">
                <input name="tag" type="text" placeholder="Tag...">
            </div>
            
            <button name="add" type="submit" class="ui button">
                Add
            </button>
        </div>
    </form>
</body>
</html>