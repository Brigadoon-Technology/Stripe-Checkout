<?php
require_once('vendor/autoload.php');

$stripe = [
  "secret_key"      => "sk_live_70pcsGaTMpjvD53CWIteqtXO",
  "publishable_key" => "pk_live_jcLviiHO48FSTNhTbGGZjPkN",
];

\Stripe\Stripe::setApiKey($stripe['secret_key']);
?>
