<div id="skin-basic-header">
<a href="/" title="Journey of Faith home"><img src="/skins/common/logo-jof.png" class="frameless" alt="Journey of Faith home"/></a>
</div>
<script type="text/javascript">
<?php
/* Choose a banner image for the header background */
$bannerlist=glob("skins/basic/banners/*.png");
$r=mt_rand(0,count($bannerlist)-1);
$banner=$bannerlist[$r];
print "document.getElementById('skin-basic-header').style.backgroundImage=\"url('".$banner."')\";"
?>
</script>
