<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/ecommerce/core/init.php';
if (!is_logged_in()) {
  login_error_redirect();
}
include 'includes/head.php';
include 'includes/navigation.php';
asdasdasd
//Delete Products
if (isset($_GET['delete'])) {
  $id = sanitize($_GET['delete']);
  $db->query("UPDATE products SET deleted = 1 WHERE id='$id'; ");
  header('Location: products.php');
}
$dbPath = '';
if (isset($_GET['add']) || isset($_GET['edit'])) {
$brand_query = $db->query("SELECT * FROM brand ORDER BY brand");
$parent_query = $db->query("SELECT * FROM categories WHERE parent='0' ORDER BY category");
$title = ((isset($_POST['title']) && $_POST['title'] != '')?sanitize($_POST['title']):'');
$brand = ((isset($_POST['brand']) && !empty($_POST['brand']))?sanitize($_POST['brand']):'');
$parent = ((isset($_POST['parent']) && !empty($_POST['parent']))?sanitize($_POST['parent']):'');
$category = ((isset($_POST['child']) && !empty($_POST['child']))?sanitize($_POST['child']):'');
$price = ((isset($_POST['price']) && $_POST['price'] != '')?sanitize($_POST['price']):'');
$list_price = ((isset($_POST['list_price']) && $_POST['list_price'] != '')?sanitize($_POST['list_price']):'');
$description = ((isset($_POST['description']) && $_POST['description'] != '')?sanitize($_POST['description']):'');
$sizes = ((isset($_POST['sizes']) && $_POST['sizes'] != '')?sanitize($_POST['sizes']):'');
$sizes = rtrim($sizes,',');
$saved_image = '';
  if(isset($_GET['edit'])){
    $edit_id = (int)$_GET['edit'];
    $productResults = $db->query("SELECT * FROM products WHERE id = '$edit_id';");
    $product = mysqli_fetch_assoc($productResults);
    if (isset($_GET['delete_image'])) {
      $image_url = $_SERVER['DOCUMENT_ROOT'].$product['image'];
      unlink($image_url);
      $db->query("UPDATE products SET image = '' WHERE id = '$edit_id'");
      header('Location: products.php?edit='.$edit_id);
    }
    $category = ((isset($_POST['child']) && $_POST['child'] != '')?sanitize($_POST['child']):$product['categories']);
    $title = ((isset($_POST['title']) && $_POST['title'] != '')?sanitize($_POST['title']):$product['title']);
    $brand = ((isset($_POST['brand']) && $_POST['brand'] != '')?sanitize($_POST['brand']):$product['brand']);
    $parentQ = $db->query("SELECT * FROM categories WHERE id = '$category'");
    $parentResult = mysqli_fetch_assoc($parentQ);
    $parent = ((isset($_POST['parent']) && $_POST['parent'] != '')?sanitize($_POST['parent']):$parentResult['parent']);
    $price = ((isset($_POST['price']) && $_POST['price'] != '')?sanitize($_POST['price']):$product['price']);
    $list_price = ((isset($_POST['list_price']))?sanitize($_POST['list_price']):$product['list_price']);
    $description = ((isset($_POST['description']))?sanitize($_POST['description']):$product['description']);
    $sizes = ((isset($_POST['sizes']) && $_POST['sizes'] != '')?sanitize($_POST['sizes']):$product['sizes']);
    $sizes = rtrim($sizes,',');
    $saved_image = (($product['image'] != '')?$product['image']:'');
    $dbPath = $saved_image;
  }
  if (!empty($sizes)) {
    $sizeString = sanitize($sizes);
    $sizeString = rtrim($sizeString,',');
    $sizesArray = explode(',', $sizeString);
    $sArray = array();
    $qArray = array();
    foreach ($sizesArray as $ss) {
      $s = explode(':', $ss);
      $sArray[] = $s[0]; //size
      $qArray[] = $s[1]; //quantity
    }
  }else {
    $sizesArray = array();
  }
if ($_POST) {
  $errors = array();
  $required = array('title', 'brand', 'price', 'parent', 'child', 'sizes');
  foreach ($required as $field) {
    if ($_POST[$field] == '') {
      $errors[] = 'All Fields With an Asterisk are required.';
      break;
    }
  }
  //Validate image
  if ($_FILES['photo']['name'] != ''){
    //$_FILES = has "photo" in the index 0 that holds an assoc array of the upload file
    $photo = $_FILES['photo'];
    $name = $photo['name'];
    $nameArray = explode('.', $name);
    $fileName = $nameArray[0];
    $fileExt = $nameArray[1];
    $mime = explode('/', $photo['type']);
    $mimeType = $mime[0];
    $mimeExt = $mime[1];
    $tmpLoc = $photo['tmp_name'];
    $fileSize = $photo['size'];
    $allowed = array('png', 'jpg', 'jpeg', 'gif');
    $uploadName = md5(microtime()).'.'.$fileExt;
    $uploadPath = BASEURL.'images/products/'.$uploadName;
    $dbPath = '/ecommerce/images/products/'.$uploadName;
    if ($mimeType != 'image') {
      $errors[] = 'The file must be an image.';
    }
    if (!in_array($fileExt, $allowed)) {
      $errors[] = 'The file extension must be a png, jpg, jpeg, or gif.';
    }
    if ($fileSize > 15000000) {
      $errors[] = 'The file size must be under 15MB.';
    }
    if ($fileExt != $mimeExt && ($mimeExt == 'jpeg' && $fileExt != 'jpg')) {
      $errors[] = 'File extension does not match the file';
    }
  }
  if (!empty($errors)) {
    echo display_errors($errors);
  }else{
    //Upload file and insert into database
    if (!empty($_FILES)) {
      move_uploaded_file($tmpLoc, $uploadPath);
    }
    $insertSql = "INSERT INTO products (`title`,`price`,`list_price`,`brand`,`categories`,`sizes`,`image`,`description`)
     VALUES ('$title','$price','$list_price','$brand','$category','$sizes','$dbPath','$description');";
     if (isset($_GET['edit'])) {
       $insertSql = "UPDATE products SET title = '$title', price = '$price', list_price = '$list_price', brand = '$brand', categories = '$category', sizes = '$sizes', image = '$dbPath', description = '$description'
       WHERE id = '$edit_id';";
     }
     $db->query($insertSql);
     header('Location: products.php');
  }
}

?>
  <h2 class="text-center"><?=((isset($_GET['edit']))?'Edit':'Add a New');?> Product</h2>
  <form class="" action="products.php?<?=((isset($_GET['edit']))?'edit='.$edit_id:'add=1');?>" method="post" enctype="multipart/form-data">
    <div class="form-group col-md-3">
      <label for="title">Title*: </label>
      <input type="text" id="title" class="form-control" name="title" value="<?=$title;?>">
    </div>
    <div class="form-group col-md-3">
      <label for="brand">Brand*: </label>
      <select class="form-control" id="brand" name="brand">
        <option value=""<?=(($brand == '')?' selected':'');?>></option>
        <?php while($b = mysqli_fetch_assoc($brand_query)): ?>
          <option value="<?=$b['id'];?>"<?=(($brand == $b['id'])?' selected':'');?>><?=$b['brand'];?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-group col-md-3">
      <label for="parent">Parent Category*: </label>
      <select class="form-control" id="parent" name="parent">
        <option value=""<?=(($parent == '')?' selected':'')?>></option>
        <?php while($p = mysqli_fetch_assoc($parent_query)): ?>
          <option value="<?=$p['id'];?>"<?=(($parent == $p['id'])?' selected':'');?>><?=$p['category'];?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-group col-md-3">
      <label for="child">Child Category*: </label>
      <select id="child" class="form-control" name="child">
      </select>
    </div>
    <div class="form-group col-md-3">
      <label for="price">Price*: </label>
      <input type="text" id="price" class="form-control" name="price" value="<?=$price;?>">
    </div>
    <div class="form-group col-md-3">
      <label for="list_price">List Price: </label>
      <input type="text" id="list_price" class="form-control" name="list_price" value="<?=$list_price;?>">
    </div>
    <div class="form-group col-md-3">
      <label >Quantity & Sizes*: </label>
      <button type="button" class="btn btn-default form-control" onclick="jQuery('#sizesModal').modal('toggle');return false;">Quantity & Sizes</button>
    </div>
    <div class="form-group col-md-3">
      <label for="sizes">Sizes * Qty Preview</label>
      <input type="text" class="form-control" name="sizes" id="sizes" value="<?=$sizes;?>" readonly>
    </div>
    <div class="form-group col-md-6">
      <?php if($saved_image != ''): ?>
        <div class="">
          <img src="<?=$saved_image;?>" alt="saved image" class="saved-image"/><br>
          <a href="products.php?delete_image=1&edit=<?=$edit_id;?>" class="text-danger">Delete Image</a>
        </div>
      <?php else: ?>
        <label for="photo">Product Photo</label>
        <input type="file" class="form-control" name="photo" id="photo">
      <?php endif; ?>
    </div>
    <div class="form-group col-md-6">
      <label for="description">Description: </label>
      <textarea id="description" name="description" class="form-control" rows="6"><?=$description;?></textarea>
    </div>
    <div class="form-group pull-right">
      <a href="products.php" class="btn btn-default">Cancel</a>
      <input type="submit" name="" value="<?=((isset($_GET['edit']))?'Edit':'Add');?> Product" class="btn btn-success">
    </div>
    <div class="clearfix">

    </div>
  </form>
  <!-- Modal from getbootstrap.com -->
  <div class="modal fade" id="sizesModal" tabindex="-1" role="dialog" aria-labelledby="sizesModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="sizesModalLabel">Size & Quantity</h4>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <?php for($i=1;$i<=12; $i++): ?>
              <div class="form-group col-md-4">
                <label for="size<?=$i;?>">Size: </label>
                <input type="text" name="size<?=$i;?>" id="size<?=$i;?>" value="<?=((!empty($sArray[$i-1]))?$sArray[$i-1]:'')?>" class="form-control">
              </div>
              <div class="form-group col-md-2">
                <label for="qty<?=$i;?>">Quantity: </label>
                <input type="number" name="qty<?=$i;?>" id="qty<?=$i;?>" value="<?=((!empty($qArray[$i-1]))?$qArray[$i-1]:'')?>" min="0" class="form-control">
              </div>
            <?php endfor; ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="updateSizes();jQuery('#sizesModal').modal('toggle');return false;">Save changes</button>
        </div>
      </div>
    </div>
  </div>
<?php }else {
$sql = "SELECT * FROM products WHERE deleted = 0;";
$product_results = $db->query($sql);
if (isset($_GET['featured'])) {
  $id = (int)$_GET['id'];
  $featured = (int)$_GET['featured'];
  $featured_sql = "UPDATE products SET featured = '$featured' WHERE id ='$id';";
  $db->query($featured_sql);
  header('Location: products.php');
}
 ?>
<h2 class="text-center">Products</h2>
<a href="products.php?add=1" class="btn btn-success pull-right" id="add-product-btn">Add Product</a>
<div class="clearfix"></div>
<hr>
<table class="table table-bordered table-condensed table-striped">
  <thead>
    <th></th>
    <th>Product</th>
    <th>Price</th>
    <th>Category</th>
    <th>Featured</th>
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
          <a href="products.php?edit=<?=$product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
          <a href="products.php?delete=<?=$product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove"></span></a>
        </td>
        <td><?=$product['title'];?></td>
        <td><?=money($product['price']);?></td>
        <td><?=$category;?></td>
        <td>
          <a href="products.php?featured=<?=(($product['featured'] == 0)?'1':'0');?>&id=<?=$product['id'];?>" class="btn btn-xs btn-default">
            <span class="glyphicon glyphicon-<?=(($product['featured']==1)?'minus':'plus');?>"></span>
          </a>
          &nbsp <?=(($product['featured'] == 1)?'Featured Product':'')?>
        </td>
        <td>0</td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>
 <?php
} #end of else
include 'includes/footer.php';?>
<script>
  jQuery('document').ready(function(){
    get_child_options('<?=$category;?>');
  });
</script>
