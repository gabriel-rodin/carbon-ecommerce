<?php
function display_errors($errors){
  $display = '<ul class="bg-danger">';
  foreach ($errors as $error) {
    $display .= '<li class="text-danger">'.$error.'</li>';
  }
  $display .= '</ul>';
  return $display;
}

function sanitize($dirty){
  return htmlentities($dirty,ENT_QUOTES,"UTF-8");
}

function money($number){
  return '₱'.a_number_format($number,2);
}

function login($user_id){
  $_SESSION['SBUser'] = $user_id;
  global $db;
  //Y = 4 digit yr
  //m = 2 digit month M = abbr month word
  //d = 2 digit day
  //H = 24 hr format h = 12 hr format
  //i = minutes
  //s = seconds
  //A = am pm
  // based on format in database
  $date = date("Y-m-d H:i:s");
  $db->query("UPDATE users SET last_login = '$date' WHERE id = '$user_id'");
  $_SESSION['success_flash'] = 'You are now logged in.';
  header('Location: index.php');
}

function is_logged_in(){
  if (isset($_SESSION['SBUser']) && $_SESSION['SBUser'] > 0) {
    return true;
  }
  return false;
}

function login_error_redirect($url = 'login.php'){
  $_SESSION['error_flash'] = 'You must be logged in to access the page';
  header('Location: '.$url);
}

function permission_error_redirect($url){
  $_SESSION['error_flash'] = 'You do not have permission to access that page';
  header('Location: '.$url);
}

function has_permission($permission = 'admin'){
  global $user_data;
  $permissions = explode(',', $user_data['permissions']);
  if (in_array($permission, $permissions, true)) {
    return true;
  }
  return false;
}

function pretty_date($date){
  return date("M d, Y h:i A", strtotime($date));
}

function get_category($child_id){
  global $db;
  $id = sanitize($child_id);
  $id = mysqli_real_escape_string($db, $id);
  $sql = "SELECT p.id AS 'pid', p.category AS 'parent', c.id AS 'cid', c.category AS 'child'
          FROM categories c
          INNER JOIN categories p
          ON c.parent = p.id
          WHERE c.id = $id";
  $query = mysqli_query($db, $sql);
  $category = mysqli_fetch_assoc($query);
  return $category;
}

function sizesToArray($string){
  $sizesArray = explode(',',$string);
  $returnArray = array();
  foreach ($sizesArray as $size) {
    $s = explode(':',$size);
    $returnArray[] = array('size' => $s[0],'quantity' => $s[1]);
  }
  return $returnArray;
}

function sizesToString($sizes){
  $sizeString = '';
  foreach ($sizes as $size) {
    $sizeString .= $size['size'].':'.$size['quantity'].',';
  }
  $trimmed = rtrim($sizeString, ',');
  return $trimmed;
}

//
// Here is a function that produces the same output as number_format() but also works with numbers bigger than 2^53.

function a_number_format($number_in_iso_format, $no_of_decimals=3, $decimals_separator='.', $thousands_separator='', $digits_grouping=3){
    // Check input variables
    if (!is_numeric($number_in_iso_format)){
        error_log("Warning! Wrong parameter type supplied in my_number_format() function. Parameter \$number_in_iso_format is not a number.");
        return false;
    }
    if (!is_numeric($no_of_decimals)){
        error_log("Warning! Wrong parameter type supplied in my_number_format() function. Parameter \$no_of_decimals is not a number.");
        return false;
    }
    if (!is_numeric($digits_grouping)){
        error_log("Warning! Wrong parameter type supplied in my_number_format() function. Parameter \$digits_grouping is not a number.");
        return false;
    }


    // Prepare variables
    $no_of_decimals = $no_of_decimals * 1;


    // Explode the string received after DOT sign (this is the ISO separator of decimals)
    $aux = explode(".", $number_in_iso_format);
    // Extract decimal and integer parts
    $integer_part = $aux[0];
    $decimal_part = isset($aux[1]) ? $aux[1] : '';

    // Adjust decimal part (increase it, or minimize it)
    if ($no_of_decimals > 0){
        // Check actual size of decimal_part
        // If its length is smaller than number of decimals, add trailing zeros, otherwise round it
        if (strlen($decimal_part) < $no_of_decimals){
            $decimal_part = str_pad($decimal_part, $no_of_decimals, "0");
        } else {
            $decimal_part = substr($decimal_part, 0, $no_of_decimals);
        }
    } else {
        // Completely eliminate the decimals, if there $no_of_decimals is a negative number
        $decimals_separator = '';
        $decimal_part       = '';
    }

    // Format the integer part (digits grouping)
    if ($digits_grouping > 0){
        $aux = strrev($integer_part);
        $integer_part = '';
        for ($i=strlen($aux)-1; $i >= 0 ; $i--){
            if ( $i % $digits_grouping == 0 && $i != 0){
                $integer_part .= "{$aux[$i]}{$thousands_separator}";
            } else {
                $integer_part .= $aux[$i];
            }
        }
    }

    $processed_number = "{$integer_part}{$decimals_separator}{$decimal_part}";
    return $processed_number;
}
