<?php
require_once '../core/init.php';
$id = $_POST['id']; #from ajax .. footer.php
$id = (int)$id; #parse int
$sql = "SELECT * FROM products WHERE id = '$id'";
$result = $db->query($sql);
$product = mysqli_fetch_assoc($result);

$brand_id = $product['brand'];
$sql = "SELECT brand FROM brand WHERE id = '$brand_id' ";
$brand_query = $db->query($sql);
$brand = mysqli_fetch_assoc($brand_query);

$sizeString = $product['sizes'];
$sizeArray = explode(',', $sizeString);
?>

<!-- Details Modal -->
<?php ob_start(); #object buffer start ?>
<div class="modal fade details-1" id="details-modal" tabindex="-1" role="dialog" aria-labelledby="details-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <!--<button class="close" type="button" data-dismiss="modal" aria-label="Close" name="button"> -->
          <button class="close" type="button" onclick="closeModal()" aria-label="Close" name="button"> <!-- The X button on top left of modal -->
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title text-center"><?= $product['title']?></h4>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
          <div class="row">
            <span id="modal_errors" class="bg-danger"></span>
            <div class="col-sm-6">
              <div class="center-block">
                <img src="<?= $product['image']?>" alt="<?= $product['title']?>" class="details img-responsive">
              </div>
            </div>
            <div class="col-sm-6">
              <h4>Details</h4>
              <p><?= nl2br($product['description'])?></p>
              <hr>
              <p>Price: P<?= $product['price']?></p>
              <p>Brand: <?= $brand['brand']?></p>
              <form action="add_cart.php" method="post" id="add_product_form">
                <input type="hidden" name="product_id" value="<?=$id;?>">
                <input type="hidden" name="available" id="available" value="">
                <div class="form-group">
                  <div class="col-xs-3">
                    <label for="quantity">Quantity:</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="0">
                  </div>
                  <br>
                </div>
                <br/><br/>
                <div class="form-group">
                  <label for="size">Size:</label>
                  <select class="form-control" name="size" id="size">
                    <option value=""></option>
                    <?php foreach($sizeArray as $string){
                      $stringArray = explode(':', $string);
                      $size = $stringArray[0];
                      $available = $stringArray[1];
                      if ($available > 0) {
                        echo '<option value="'.$size.'" data-available="'.$available.'">'.$size.' ('.$available.' Available)</option>';
                      }
                    }
                    ?>

                  </select>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <!--<button class="btn btn-default" data-dismiss="modal">Close</button> -->
        <button class="btn btn-default" onclick="closeModal()">Close</button>
        <button class="btn btn-warning" onclick="add_to_cart();return false;"><span class="glyphicon glyphicon-shopping-cart"></span>Add to Cart</button>
      </div>
    </div>
  </div>
</div>
<script>
  jQuery('#size').change(function(){
    var available = jQuery('#size option:selected').data('available');
    jQuery('#available').val(available);
  });
  function closeModal(){
    jQuery('#details-modal').modal('hide'); // .modal is a bootstrap.js fnction
    setTimeout(function(){
      jQuery('#details-modal').remove(); // .remove is a jquery fnction
      jQuery('.modal-backdrop').remove();
    },500);
  }
</script>
<?php echo ob_get_clean(); #object buffer clean ?>
