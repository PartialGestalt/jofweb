<?php
//
// form-popup-pre.php -- Skin-specific frame decoration for a popup picker
//
// Externals: 
//   targetDiv -- ID of popup div.
global $targetDiv;
?>

<?php
// Step 1: Simple X in the upper-right corner for closing the popup.
//
print '<div class="form-popup-decoration">';
print '<span class="form-popup-title">';
print 'Choose a date';
print '</span>';
print '<a ';
print '  href="javascript:ak_hideElementById(\''.$targetDiv.'\');" ';
print '  title="Close Popup" ';
print '>';
print '<img ';
print '  alt="Close Popup" ';
print '  class="form-popup-close frameless" ';
print '  src="skins/'.skin_getName().'/popup-frame-close.png" ';
print '/>';
print '</a>';
print '</div>';

// Step 2: Closure div
print '<div class="closure-div"></div>';
?>
