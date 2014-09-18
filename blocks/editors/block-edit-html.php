<div class="block-editor block-editor-html">
<?php
require_once("library/core/class-form.php");
//
// BLOCK: Edit a server-side HTML file.
//
// Required parameters:
// -------------------
// @param $editor_filename The relative path to the file to edit.
//
// Optional parameters:
// -------------------
// @param $editor_option_preview Add a preview button.
//

$editor_filename = block_getParameter('editor-filename');
$editor_hostpage = block_getParameter('editor-hostpage');
$editor_processor = block_getParameter('editor-processor');
$editor_block_width = block_getParameter('block-width');
$editor_width = ''.(0.95 * $editor_block_width).'px';

if (null == $editor_filename)
{
   print('Something is wrong.');
   return;
}
// Step 1: Create the base form
$editor = new akForm("edit-html-form",$editor_processor,FORM_METHOD_POST);

// Step 2: Generate items for the form */
  // Hidden reference to the file and host page
$editor->createSimpleItem("hidden-filename-item",FORM_ITEM_HIDDEN,"file",$editor_filename);
$editor->createSimpleItem("hidden-hostpage-item",FORM_ITEM_HIDDEN,"hostpage",$editor_hostpage);
  // Main text area 
$textarea = new akFormItem("file-text",FORM_ITEM_TEXTAREA,"edit-html-contents",file_get_contents($editor_filename));
$textarea->addStyle('width',$editor_width);
$textarea->vsize=15;
$editor->adopt($textarea);
  // Submit/cancel buttons
$editor->createSimpleItem("submit-item",FORM_ITEM_SUBMIT,"submit-item","Save changes");
$canit = new akFormItem("cancel-button",FORM_ITEM_BUTTON,"cancel-item","Cancel");
$canit->addEvent("onClick","window.location.replace('".$editor_hostpage."');");
$editor->adopt($canit);

// Step 3: Emit the form
$editor->emit();

?>
</div>
