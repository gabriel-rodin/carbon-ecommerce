<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/init.php';
if (!is_logged_in()) {
  login_error_redirect();
}
include 'includes/head.php';
include 'includes/navigation.php';

$sql = "SELECT * FROM products WHERE deleted = 1;";
$product_results = $db->query($sql);
if (isset($_GET['archived'])) {
  $archive_id = $_GET['archived'];
  $archive_sql = "UPDATE products SET deleted = 0 WHERE id ='$archive_id';";
  $db->query($archive_sql);
  header('Location: archived.php');
}
?>

<h2 class="text-center">Archived Products</h2>
<div class="clearfix"></div>
<hr>
<table class="table table-bordered table-condensed table-striped">
  <thead>
    <th></th>
    <th>Product</th>
    <th>Price</th>
    <th>Category</th>
    <th>Sold</th>
  </thead>
  <tbody>
    <?php while($product = mysqli_fetch_assoc($product_results)):
        $child_id = $product['categories'];
        $category_sql = "SELECT * FROM categories WHERE id = '$child_id';";
        $category_result = $db->query($category_sql);
        $child = mysqli_fetch_assoc($category_result);
        $parent_id = $child['parent'];
        $parent_sql = "SELECT * FROM categories WHERE id = '$parent_id';";
        $parent_result = $db->query($parent_sql);
        $parent = mysqli_fetch_assoc($parent_result);
        $category = $parent['category'].'~'.$child['category'];
      ?>
      <tr>
        <td>
          <a href="archived.php?archived=<?=$product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-refresh"></span></a>
        </td>
        <td><?=$product['title'];?></td>
        <td><?=money($product['price']);?></td>
        <td><?=$category;?></td>
        <td><?=$product['sold'];?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php include 'includes/footer.php';?>
