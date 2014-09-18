<?php
// 
// block-login-fail-header -- PHP/HTML to present before the login form
//                            on the "try again" page.
//
?>
<h2 class="story-headline">Login Failure</h2>
<span class="story-span">
Dude, that was totally wrong.  You can try again here, or just
<?php 
echo "<a href=\"".$login_form_referrer."\" title=\"Continue without logging in\">go back to what you were doing</a>";
?>
</span>
