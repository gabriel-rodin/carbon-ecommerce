<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/core/init.php';
$parent_id = (int)$_POST['parentID']; // parentID from ajax request in footer
$selected = sanitize($_POST['selected']);
$child_query = $db->query("SELECT * FROM categories WHERE parent='$parent_id' ORDER BY category");
ob_start();
 ?>
  <option value=""></option>
  <?php while($child = mysqli_fetch_assoc($child_query)): ?>
    <option value="<?=$child['id'];?>"<?=(($selected == $child['id'])?' selected':'')?>><?=$child['category'];?></option>
  <?php endwhile; ?>
 <?php echo ob_get_clean(); ?>
