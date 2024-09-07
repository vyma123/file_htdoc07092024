<?php
include("db.php");

$products_per_page = 4; // Define the number of products per page

// current page, default: 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

// offset for query 
$offset = ($current_page - 1) * $products_per_page;

// total product with filter
$total_products_query = "SELECT COUNT(*) as total FROM products WHERE 1=1";
$query = "SELECT * FROM products WHERE 1=1";

// search
if (isset($_POST['search_btn'])) {
    $search = $_POST['search'];
    $search = trim($search);
    $total_products_query .= " AND title LIKE '%$search%'";
    $query .= " AND title LIKE '%$search%'";
}

if (isset($_GET['cat_filter']) && !empty($_GET['cat_filter'])) {
    $cat_filter = $_GET['cat_filter'];
    $query .= " AND products.id IN (SELECT product_id FROM product_cat WHERE cat_id = '$cat_filter')";
}

// tag filter
if (isset($_GET['tag_filter']) && !empty($_GET['tag_filter'])) {
    $tag_filter = $_GET['tag_filter'];
    $query .= " AND products.id IN (SELECT product_id FROM product_tags WHERE tag_id = '$tag_filter')";
}

$selected_cat = isset($_GET['cat_filter']) ? $_GET['cat_filter'] : '';
$selected_tag = isset($_GET['tag_filter']) ? $_GET['tag_filter'] : '';

// sort, date, price filter
$sort_option = "";
$date_filter = "";
$begin_date = "";
$end_date = "";
$price_from = "";
$price_to = "";

if (isset($_GET['sort_alphabet'])) {
    $sort_option = $_GET['sort_alphabet'] == 'a-z' ? "ASC" : "DESC";
}

if (isset($_GET['date_filter'])) {
    $date_filter = $_GET['date_filter'];
}

if (isset($_GET['begin_date']) && !empty($_GET['begin_date'])) {
    $begin_date = $_GET['begin_date'];
    $total_products_query .= " AND date >= '$begin_date'";
    $query .= " AND date >= '$begin_date'";
}

if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
    $end_date = $_GET['end_date'];
    $total_products_query .= " AND date <= '$end_date'";
    $query .= " AND date <= '$end_date'";
}

if (isset($_GET['price_from']) && !empty($_GET['price_from'])) {
    $price_from = $_GET['price_from'];
    $total_products_query .= " AND price >= '$price_from'";
    $query .= " AND price >= '$price_from'";
}

if (isset($_GET['price_to']) && !empty($_GET['price_to'])) {
    $price_to = $_GET['price_to'];
    $total_products_query .= " AND price <= '$price_to'";
    $query .= " AND price <= '$price_to'";
}

if ($date_filter == "today") {
    $today = date('Y-m-d');
    $total_products_query .= " AND date = '$today'";
    $query .= " AND date = '$today'";
} elseif ($date_filter == "yesterday") {
    $yesterday = date('Y-m-d', strtotime('-1 day'));
    $total_products_query .= " AND date = '$yesterday'";
    $query .= " AND date = '$yesterday'";
} elseif ($date_filter == "this_week") {
    $start_week = date('Y-m-d', strtotime('monday this week'));
    $end_week = date('Y-m-d', strtotime('sunday this week'));
    $total_products_query .= " AND date BETWEEN '$start_week' AND '$end_week'";
    $query .= " AND date BETWEEN '$start_week' AND '$end_week'";
}

if ($sort_option) {
    $query .= " ORDER BY title $sort_option";
}

// Add LIMIT and OFFSET for pagination
$query .= " LIMIT $products_per_page OFFSET $offset";

$total_products_result = mysqli_query($conn, $total_products_query);
// get a row from result query return associative array
$total_products_row = mysqli_fetch_assoc($total_products_result);
// get value
$total_products = $total_products_row['total'];

// Calculate total page
$total_pages = ceil($total_products / $products_per_page);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- semantic ui -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.5.0/semantic.min.css" integrity="sha512-KXol4x3sVoO+8ZsWPFI/r5KBVB/ssCGB5tsv2nVOKwLg33wTFP3fmnXa47FdSVIshVTgsYk/1734xSk9aFIa4A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- css -->
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>PHP1</h1>
    <header>
        <div class="ui secondary menu">
            <a href="add.php" class="ui button openModal">Add product</a>
            <a href="add_property.php" class="ui button">Add property</a>
            <a class="ui button">Sync from VillaTheme</a>
            <div class="right menu">
                <form action="" method="post">
                    <div class="item">
                        <div class="ui icon input">
                            <input name="search" class="search_input" type="text" placeholder="Search product...">
                            <button name="search_btn" class="search_btn">
                                <i class="search link icon"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <form action="" method="GET">
            <div class="filter_box">
                <div>
                    <select name="date_filter" class="ui dropdown">
                        <option value="">Date</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="this_week">This Week</option>
                    </select>
                </div>
                <div>
                    <select name="sort_alphabet" class="asc ui dropdown">
                        <option value="a-z" <?php if (isset($_GET['sort_alphabet']) && $_GET['sort_alphabet'] == "a-z") echo 'selected'; ?>>ASC</option>
                        <option value="z-a" <?php if (isset($_GET['sort_alphabet']) && $_GET['sort_alphabet'] == "z-a") echo 'selected'; ?>>DESC</option>
                    </select>
                </div>
                <div>
                    <select name="cat_filter" class=" cate ui dropdown">
                        <option value="">Category</option>
                        <?php
                        $cat_query = "SELECT * FROM category";
                        $cat_result = mysqli_query($conn, $cat_query);
                        while ($cat_row = mysqli_fetch_assoc($cat_result)) {
                            echo '<option value="' . $cat_row['id'] . '"';
                            if ($selected_cat == $cat_row['id']) {
                                echo ' selected';
                            }
                            echo '>' . $cat_row['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <select name="tag_filter" class="tag ui dropdown">
                        <option value="">Select tag</option>
                        <?php
                        $tag_query = "SELECT * FROM tags";
                        $tag_result = mysqli_query($conn, $tag_query);
                        while ($tag_row = mysqli_fetch_assoc($tag_result)) {
                            echo '<option value="' . $tag_row['id'] . '"';
                            if ($selected_tag == $tag_row['id']) {
                                echo ' selected';
                            }
                            echo '>' . $tag_row['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="ui input">
                    <input type="date" name="begin_date" value="<?php echo isset($_GET['begin_date']) ? $_GET['begin_date'] : ''; ?>">
                </div>
                <div class="ui input">
                    <input type="date" name="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">
                </div>
                <div class="ui input">
                    <input type="text" name="price_from" placeholder="Price from" value="<?php echo isset($_GET['price_from']) ? $_GET['price_from'] : ''; ?>">
                </div>
                <div class="ui input">
                    <input type="text" name="price_to" placeholder="Price to" value="<?php echo isset($_GET['price_to']) ? $_GET['price_to'] : ''; ?>">
                </div>
                <button type="submit" class="filter ui button">Filter</button>
            </div>
        </form>
    </header>
    <section class="table_box">
        <table class="ui celled table">
            <?php
            $query_run = mysqli_query($conn, $query);

            if (mysqli_num_rows($query_run) > 0) {
                echo "<thead>
                    <tr>
                        <th>Date</th>
                        <th>Product name</th>
                        <th>SKU</th>
                        <th>Price</th>
                        <th>Feature Image</th>
                        <th>Gallery</th>
                        <th>Categories</th>
                        <th>Tags</th>
                        <th>Action</th>
                    </tr>
                    </thead>";
                foreach ($query_run as $row) {
                    $date = $row['date'];
                    $phpdate = strtotime($date);
                    $mysqldate = date('m-d-Y', $phpdate);
                    $title = $row['title'];
                    $sku = $row['sku'];
                    $price = $row['price'];
                    $featured_image = $row['featured_image'];
            ?>
                    <tbody>
                        <tr>
                            <td><?php echo $mysqldate; ?></td>
                            <td><?php echo $title; ?></td>
                            <td><?php echo $sku; ?></td>
                            <td><?php echo $price; ?></td>
                            <td><?php echo $featured_image; ?></td>
                            <td>
                                <?php
                                $catsql1 = "SELECT gallery.name FROM gallery 
                                            INNER JOIN product_gallery ON gallery.id = product_gallery.gallery_id 
                                            WHERE product_gallery.product_id= '" . $row['id'] . "'";
                                $catresult1 = mysqli_query($conn, $catsql1);
                                while ($catdata = mysqli_fetch_array($catresult1)) {
                                    echo $catdata['name'] . ',';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $catsql = "SELECT category.name FROM category 
                                            INNER JOIN product_cat ON category.id = product_cat.cat_id 
                                            WHERE product_cat.product_id= '" . $row['id'] . "'";
                                $catresult = mysqli_query($conn, $catsql);
                                while ($catdata = mysqli_fetch_array($catresult)) {
                                    echo $catdata['name'] . ',';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $catsql = "SELECT tags.name FROM tags 
                                            INNER JOIN product_tags ON tags.id = product_tags.tag_id 
                                            WHERE product_tags.product_id= '" . $row['id'] . "'";
                                $catresult = mysqli_query($conn, $catsql);
                                while ($catdata = mysqli_fetch_array($catresult)) {
                                    echo $catdata['name'] . ',';
                                }
                                ?>
                            </td>
                            <td data-label="Job">
                                <a href="edit.php?editid=<?php echo $row['id']; ?>" class="btn_edit openModal_edit">
                                    <i class="edit icon"></i>
                                </a>
                                <a href="delete.php?deleteid=<?php echo $row['id']; ?>" class="btn_delete">
                                    <i class="trash icon"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
            <?php
                }
            } else {
                echo '<h2>Data not found</h2>';
            }
            ?>
        </table>
        <div class="box_navigation">
            <div aria-label="Pagination Navigation" role="navigation" class="ui pagination menu">
                <?php if ($current_page > 1) : ?>
                    <a href="?page=<?php echo $current_page - 1; ?>&date_filter=<?php echo $date_filter; ?>&sort_alphabet=<?php echo $sort_option; ?>&begin_date=<?php echo $begin_date; ?>&end_date=<?php echo $end_date; ?>&price_from=<?php echo $price_from; ?>&price_to=<?php echo $price_to; ?>" class="item">
                        <i class="arrow left icon"></i>

                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <a href="?page=<?php echo $i; ?>&date_filter=<?php echo $date_filter; ?>&sort_alphabet=<?php echo $sort_option; ?>&begin_date=<?php echo $begin_date; ?>&end_date=<?php echo $end_date; ?>&price_from=<?php echo $price_from; ?>&price_to=<?php echo $price_to; ?>" class="item <?php if ($current_page == $i) echo 'active'; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages) : ?>
                    <a href="?page=<?php echo $current_page + 1; ?>&date_filter=<?php echo $date_filter; ?>&sort_alphabet=<?php echo $sort_option; ?>&begin_date=<?php echo $begin_date; ?>&end_date=<?php echo $end_date; ?>&price_from=<?php echo $price_from; ?>&price_to=<?php echo $price_to; ?>" class="item">
                        <i class="arrow right icon"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
</body>

</html>