<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/core/init.php';
  //post data from ajax in cart.php
  $name = sanitize($_POST['full_name']);
  $email = sanitize($_POST['email']);
  $street = sanitize($_POST['street']);
  $street2 = sanitize($_POST['street2']);
  $city = sanitize($_POST['city']);
  $state = sanitize($_POST['state']);
  $zip_code = sanitize($_POST['zip_code']);
  $country = sanitize($_POST['country']);
  $errors = array();
  $required = array(
    'full_name'  => 'Full Name',
    'email'      => 'Email',
    'street'     => 'Street Address',
    'city'       => 'City',
    'state'      => 'State',
    'zip_code'   => 'Zip Code',
    'country'    => 'Country'
  );

  //check if all required fields are filled out
  foreach ($required as $f => $d) {
    if (empty($_POST[$f]) || $_POST[$f] == '') {
      $errors[] = $d.' is required.';
    }
  }

  //check if valid email address
  if ($_POST['email'] != '' || !empty($_POST['email'])) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Please enter valid email.";
    }
  }


  if (!empty($errors)) {
    echo display_errors($errors);
  }else {
    //validation passed
    echo 'passed';
  }

 ?>
