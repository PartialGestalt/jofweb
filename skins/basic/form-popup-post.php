<?php
//
// form-popup-post.php -- Skin-specific frame decoration for a popup picker
//
// Externals: 
//   targetDiv -- ID of popup div.
global $targetDiv;
?>

<?php
// Step 1: Simple X in the upper-right corner for closing the popup.
//
print '<div class="form-popup-decoration">';
print '<a ';
print '  href="javascript:ak_hideElementById(\''.$targetDiv.'\');" ';
print '  title="Close Popup" ';
print '>';
print '<span class="form-popup-close">';
print 'close';
print '</span>';
print '</a>';
print '</div>';

// Step 2: Closure div
print '<div class="closure-div"></div>';
?>
