<?php
  require_once 'core/init.php';
  include 'includes/head.php';
  include 'includes/navigation.php';
  include 'includes/headerpartial.php';

  if ($cart_id != '') {
    $cartQ = $db->query("SELECT * FROM cart WHERE id = '{$cart_id}';");
    $result = mysqli_fetch_assoc($cartQ);
    $items = json_decode($result['items'],true); // true returns an assoc array instead of object
    $i = 1;
    $sub_total = 0;
    $item_count = 0;
  }
 ?>

<div class="col-md-12">
  <div class="row">
    <h2 class="text-center">My Shopping Cart</h2><hr>
    <?php if($cart_id == ''): ?>
      <div class="bg-danger">
        <p class="text-center text-danger">
          Your shopping cart is empty.
        </p>
      </div>
    <?php else: ?>
      <table class="table table-bordered table-condensed table-striped">
        <thead>
          <th>#</th>
          <th>Item</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Size</th>
          <th>Sub Total</th>
        </thead>
        <tbody>
          <?php
            foreach ($items as $item) {
              $product_id = $item['id'];
              $productQ = $db->query("SELECT * FROM products WHERE id = '{$product_id}';");
              $product = mysqli_fetch_assoc($productQ);
              $sArray = explode(',',$product['sizes']);
              foreach($sArray as $sizeString){
                $s = explode(':',$sizeString);
                if ($s[0] == $item['size']) {
                  $available = $s[1];
                }
              }
              ?>
              <tr>
                <td><?=$i;?></td>
                <td><?=$product['title'];?></td>
                <td><?=money($product['price']);?></td>
                <td>
                  <button class="btn btn-xs btn-default" onclick="update_cart('removeOne','<?=$product['id'];?>','<?=$item['size'];?>')">-</button>
                  <?=$item['quantity'];?>
                  <?php if($item['quantity'] < $available): ?>
                    <button class="btn btn-xs btn-default" onclick="update_cart('addOne','<?=$product['id'];?>','<?=$item['size'];?>')">+</button>
                  <?php else: ?>
                    <span class="text-danger">Max</span>
                  <?php endif;?>
                </td>
                <td><?=$item['size'];?></td>
                <td><?=money($item['quantity'] * $product['price']);?></td>
              </tr>
              <?php
              $i++;
              $item_count += $item['quantity'];
              $sub_total += ($product['price'] * $item['quantity']);
            }
            $tax = TAXRATE * $sub_total;
            $tax = a_number_format($tax,2);
            $grand_total = $tax + $sub_total;
          ?>
        </tbody>
      </table>
      <table class="table table-bordered table-condensed text-right">
        <legend>Totals</legend>
        <thead class="totals-table-header">
          <th>Total Items</th>
          <th>Sub Total</th>
          <th>Tax</th>
          <th>Grand Total</th>
        </thead>
        <tbody>
          <tr>
            <td><?=$item_count;?></td>
            <td><?=money($sub_total);?></td>
            <td><?=money($tax);?></td>
            <td class="bg-success"><?=money($grand_total);?></td>
          </tr>
        </tbody>
      </table>
      <!-- Checkout button -->
      <button type="button" class="btn btn-primary btn-lg pull-right" data-toggle="modal" data-target="#checkoutModal">
        <span class="glyphicon glyphicon-shopping-cart"></span> Check Out >>
      </button>

      <!-- Modal -->
      <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="checkoutModalLabel">Shipping Information</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <form action="./thankYou.php" method="post" id="payment-form">
                  <span class="bg-danger" id="payment-errors"></span>
                  <input type="hidden" name="tax" value="<?=$tax;?>">
                  <input type="hidden" name="sub_total" value="<?=$sub_total;?>">
                  <input type="hidden" name="grand_total" value="<?=$grand_total;?>">
                  <input type="hidden" name="cart_id" value="<?=$cart_id;?>">
                  <input type="hidden" name="description" value="<?=$item_count.' item'.(($item_count>1)?'s':'').' from Carbons Ecommerce.';?>">
                  <div class="form-row">
                    <div class="" id="step1" style="display:block">
                      <div class="form-group col-md-6">
                        <label for="full_name">Full Name: </label>
                        <input class="form-control mb3 StripeElement StripeElement--empty" id="full_name" type="text" name="full_name" value="">
                      </div>
                      <div class="form-group col-md-6">
                        <label for="email">Email: </label>
                        <input class="form-control mb3 StripeElement StripeElement--empty" id="email" type="email" name="email" value="">
                      </div>
                      <div class="form-group col-md-6">
                        <label for="street">Street Address: </label>
                        <input class="form-control mb3 StripeElement StripeElement--empty" id="street" type="text" name="street" value="" data-stripe="address_line1">
                      </div>
                      <div class="form-group col-md-6">
                        <label for="street2">Street Address 2: </label>
                        <input class="form-control mb3 StripeElement StripeElement--empty" id="street2" type="text" name="street2" value="" data-stripe="address_line2">
                      </div>
                      <div class="form-group col-md-6">
                        <label for="city">City: </label>
                        <input class="form-control mb3 StripeElement StripeElement--empty" id="city" type="text" name="city" value="" data-stripe="address_city">
                      </div>
                      <div class="form-group col-md-6">
                        <label for="state">State: </label>
                        <input class="form-control mb3 StripeElement StripeElement--empty" id="state" type="text" name="state" value="" data-stripe="address_state">
                      </div>
                      <div class="form-group col-md-6">
                        <label for="zip_code">Zip Code: </label>
                        <input class="form-control mb3 StripeElement StripeElement--empty" id="zip_code" type="text" name="zip_code" value="" data-stripe="address_zip">
                      </div>
                      <div class="form-group col-md-6">
                        <label for="country">Country: </label>
                        <input class="form-control mb3 StripeElement StripeElement--empty" id="country" type="text" name="country" value="" data-stripe="address_country">
                      </div>
                    </div>
                    <div class="" id="step2" style="display:none">
                      <div class="form-group col-md-12">
                        <label for="name">Name on Card: </label>
                        <input type="text" id="name" class="form-control mb3 StripeElement StripeElement--empty" data-stripe="name">
                      </div>
                      <div id="card-element" class="form-control col-md-12">
                      <!-- A Stripe Element will be inserted here. -->
                        <CardElement hidePostalCode=true />
                      </div>
                    </div>
                      <!-- Used to display form errors. -->
                      <div id="card-errors" role="alert"></div>
                  </div>
              </div>
              <script src="https://js.stripe.com/v3/"></script>
              <script src="js/charge.js"></script>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary" onclick="check_address();" id="next_button">Next >></button>
              <button type="button" class="btn btn-primary" onclick="back_address();" id="back_button" style="display:none"><< Back</button>
              <button type="Submit" class="btn btn-success" id="checkout_button" style="display:none">Checkout</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php endif;?>
  </div>
</div>
<script>
  function back_address(){
    jQuery('#payment-errors').html("");
    jQuery('#step1').css("display","block");
    jQuery('#step2').css("display","none");
    jQuery('#next_button').css("display","inline-block");
    jQuery('#back_button').css("display","none");
    jQuery('#checkout_button').css("display","none");
    jQuery('#checkoutModalLabel').html("Shipping Address");
  };
  function check_address(){
    var data = {
      'full_name' : jQuery('#full_name').val(),
      'email' : jQuery('#email').val(),
      'street' : jQuery('#street').val(),
      'street2' : jQuery('#street2').val(),
      'city' : jQuery('#city').val(),
      'state' : jQuery('#state').val(),
      'zip_code' : jQuery('#zip_code').val(),
      'country' : jQuery('#country').val()
    };
    jQuery.ajax({
      url : '/admin/parsers/check_address.php',
      method : 'post',
      data : data,
      success : function(data){
        //data is data returned(echo) from check_address.php
        if (data != 'passed') {
          jQuery('#payment-errors').html(data);
        }
        if (data == 'passed') {
          jQuery('#payment-errors').html("");
          jQuery('#step1').css("display","none");
          jQuery('#step2').css("display","block");
          jQuery('#next_button').css("display","none");
          jQuery('#back_button').css("display","inline-block");
          jQuery('#checkout_button').css("display","inline-block");
          jQuery('#checkoutModalLabel').html("Enter Your Card Details");
        }
      },
      //error : function(){alert("Something Went Wrong");}
      error: function (xhr, ajaxOptions, thrownError) {
        alert(xhr.status);
        alert(thrownError);
      }
    });
  }

  //Stripe.setPublishableKey('?=//STRIPE_PUBLIC;?'); //constant created from config.php
/*
  var stripe = Stripe('pk_test_2fT0Con7tdkeTVXAxAj24EhZ');
  var elements = stripe.elements();

  function stripeTokenHandler(token) {
    // Insert the token ID into the form so it gets submitted to the server
    var form = document.getElementById('payment-form');
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'stripeToken');
    hiddenInput.setAttribute('value', token.id);
    form.appendChild(hiddenInput);

    // Submit the form
    form.submit();
  }

  // Create a token or display an error when the form is submitted.
  var form = document.getElementById('payment-form');
  form.addEventListener('submit', function(event) {
  event.preventDefault();

  stripe.createToken(card).then(function(result) {
    if (result.error) {
      // Inform the customer that there was an error.
      var errorElement = document.getElementById('payment-errors');
      errorElement.textContent = result.error.message;
    } else {
      // Send the token to your server.
      stripeTokenHandler(result.token);
    }
  });
});
*/
  /*
  function stripeResponseHandler(status, response) {
    var $form = $('#payment-form');

    if (response.error) {
      // Show the errors on the form
      $form.find('#payment-errors').text(response.error.message);
      $form.find('button').prop('disabled', false);
    }else {
      // response contains id and card, which contains additional card details
      var token = response.id;
      // insert the token into the form so it gets submitted to the server
      $form.append($('<input type="hidden" name="stripeToken" />').val(token));
      // and submit
      $form.get(0).submit();
    }
  };

  jQuery(function($) {
    $('#payment-form').submit(function(event){
      var $form = $(this);

      // Disable the submit button to prevent repeated clicks
      $form.find('button').prop('disabled',true);

      Stripe.card.createToken($form, stripeResponseHandler);

      // Prevent the form from submitting with the default action
      return false;
    });
  });*/
</script>
 <?php
   include 'includes/footer.php';
  ?>
