<?php
require_once 'core/init.php';

\Stripe\Stripe::setApiKey('sk_test_NXiQBRmDjWsmJcsh1XRLdHvy');
$POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);

$token = $POST['stripeToken'];
// GET the rest of the post data
$full_name = $POST['full_name'];
$email = $POST['email'];
$street = $POST['street'];
$street2 = $POST['street2'];
$city = $POST['city'];
$state = $POST['state'];
$zip_code = $POST['zip_code'];
$country = $POST['country'];
$tax = $POST['tax'];
$sub_total = $POST['sub_total'];
$grand_total = $POST['grand_total'];
$cart_id = $POST['cart_id'];
$description = $POST['description'];
$charge_amount = $grand_total * 100;
//Create Customer in Stripe/*
$customer = \Stripe\Customer::create(array(
  "email" => $email,
  "source" => $token
));

//Charge Customer
$charge = \Stripe\Charge::create(array(
  'amount' => $charge_amount, // amount in cents
  'currency' => 'php',
  'description' => $description,
  'receipt_email' => $email,
  "customer" => $customer->id
));

//adjust inventory
$itemQ = $db->query("SELECT * FROM cart WHERE id = '{$cart_id}';");
$iresults = mysqli_fetch_assoc($itemQ);
$items = json_decode($iresults['items'],true); //converts string to an associative array
foreach ($items as $item) {
  $newSizes = array();
  $item_id = $item['id'];
  $productQ = $db->query("SELECT sizes FROM products WHERE id = '{$item_id}';");
  $product = mysqli_fetch_assoc($productQ);
  $sizes = sizesToArray($product['sizes']);
  foreach ($sizes as $size) {
    if ($size['size'] == $item['size']) {
      $q = $size['quantity'] - $item['quantity'];
      $newSizes[] = array('size' => $size['size'],'quantity' => $q);
    }else {
      $newSizes[] = array('size' => $size['size'],'quantity' => $size['quantity']);
    }
  }
  $sizeString = sizesToString($newSizes);
  $db->query("UPDATE products SET sizes ='{$sizeString}' WHERE id = '{$item_id}';");
}
//update cart
$chargeID = $charge->id;
$chargeOBJ = $charge->object;
$sql ="INSERT INTO transactions (charge_id,cart_id,full_name,email,street,street2,city,state,zip_code,country,sub_total,tax,grand_total,description,txn_type)
VALUES ('$chargeID','$cart_id','$full_name','$email','$street','$street2','$city','$state','$zip_code','$country','$sub_total','$tax','$grand_total','$description','$chargeOBJ')";
$db->query($sql);

$db->query("UPDATE cart SET paid = 1 WHERE id = '{$cart_id}';");


  $domain = ($_SERVER['HTTP_HOST'] != 'localhost')? '.'.$_SERVER['HTTP_HOST']:false;
  setcookie(CART_COOKIE,'',1,"/",$domain,false);
  include 'includes/head.php';
  include 'includes/navigation.php';
  include 'includes/headerpartial.php';
?>
  <h1 class="text-center text-success">Thank You!</h1>
  <p> Your card has been successfully charged <?=money($grand_total);?>. You have been emailed a receipt. Please
      check your spam folder if it is not in your inbox. You can print this page as a receipt.</p>
  <p> Your receipt number is: <strong><?=$cart_id;?></strong></p>
  <p> Your order will be shipped to the address below.</p>
  <address class="">
    <?=$full_name;?><br>
    <?=$street;?><br>
    <?=(($street2 != '')?$street2.'<br>':'');?>
    <?=$city.', '.$state.' '.$zip_code;?><br>
    <?=$country;?><br>
  </address>
<?php
  include 'includes/footer.php';
 ?>
