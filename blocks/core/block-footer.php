<?php
// If the skin has pre-content setup, include it here.
require_once("library/core/util-skin.php");
skin_include("block-footer-pre.php");
?>
<div id="footer-div">
<div id="footer-copyright-div">
<span class="footer-text">Copyright &copy; 2010 Journey Of Faith UMC</span>
</div>
<div id="footer-contact-div">
<span class="footer-text">
<span>Where to send us mail: PO Box 1343, RR 78680-1343</span>,<br/>
<span>How to find us: <a href="/map.php" title="Directions to the Church">7301 County Road 110, Round Rock, Texas, 78634</a></span>,<br/>
<span>How to call us: (512)&nbsp;255-8403</span><br/>
</span>
<span class="footer-text">
<?php
require_once("library/core/class-user.php");
if ($auth_user->name == "default") {
  print 'Not logged in.';
}
else
{
  print 'Logged in as "'.$auth_user->name.'" ('.$auth_user->fullname.').';
}
?>
</span>
</div>
<div id="footer-validator-div">
<?php
// We're not valid XHTML, because we use document.writeln() as we're loading.  Fix this before putting the button back.
//<a href="http://validator.w3.org/check?uri=referer"><img src="skins/common/valid-xhtml10-blue.png" alt="Valid XHTML 1.0 Transitional" style="height: 31px; width: 88px; border:0" /></a>
?>
<a href="http://jigsaw.w3.org/css-validator/check/referer"><img style="border:0;width:88px;height:31px" src="skins/common/valid-css2-blue.png" alt="Valid CSS!" /></a>
</div>
</div>
<?php
// If the skin has post-content setup, include it here.
skin_include("block-footer-post.php");
?>
