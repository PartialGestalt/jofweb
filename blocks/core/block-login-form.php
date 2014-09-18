<?php
require_once("library/core/class-form.php");
/* Step 1: Create the form object */
$login = new akForm("login-form","/login-processor.php",FORM_METHOD_POST);
/* Step 2: Create and add items */
  /* Step 2.1: State in hidden fields */
$login->createSimpleItem("login-item-referrer",FORM_ITEM_HIDDEN,"referrer",$login_form_referrer);
$login->createSimpleItem("login-item-action",FORM_ITEM_HIDDEN,"auth-action","login");
  /* Step 2.2: Username/password fields */
$item = new akFormItem("login-item-username",FORM_ITEM_TEXT,"username");
$item->label="Username:";
$item->hsize=32; 
$item->max_hsize=256;
$item->hint="Enter username or email address...";
$login->adopt($item);

$item = new akFormItem("login-item-password",FORM_ITEM_PASSWORD,"password");
$item->label="Password:";
$item->hsize=32; 
$item->max_hsize=256;
$item->hint="Enter password...";
$login->adopt($item);

  /* Step 2.3: Submit button */
$item = new akFormItem("login-item-submit",FORM_ITEM_SUBMIT,"submit","Log in");
$login->adopt($item);

/* Step 3: Emit the form */
$login->emit();
?>
