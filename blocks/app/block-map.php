<?php
// If the skin has pre-content setup, include it here.
require_once("library/core/util-skin.php");
skin_include("block-map-pre.php");
?>
<div id="block-map-div" class="block-outer-div block-div">
<div id="block-map-maps-div">

<?php
// Build service list of known services
require_once("library/app/class-akwebservice.php");
$mapsrv = array(
  "google" => new akWebService("google","Google Maps","blocks/app/block-map-google.php"),
  "yahoo"  => new akWebService("yahoo","Yahoo! Maps","blocks/app/block-map-yahoo.php"),
  "mapquest" => new akWebService("mapquest","Mapquest","blocks/app/block-map-mapquest.php")
);
// Determine selected service from config
$mapper=config_getValue("map");
if ($mapper=="default") {
  // Nothing set, default to Google Maps
  $mapper="google";
}
else
{
  $expire_time=time()+300000000;
  // User-specified, store as cookie if changed
  if ($mapper != config_getValue("map",CONFIG_SOURCE_COOKIE)) {
    echo "<script type=\"text/javascript\">";
    echo "document.cookie=\"".CONFIG_COOKIE_PREFIX."map=".$mapper.";expires=".$expire_time."\"";
    echo "</script>";
  }
}
require_once($mapsrv[$mapper]->url);
?>

</div>
<div id="block-map-alternates-div" class="block-text">
<?php
// Alternate services text
echo "(Don't like ".$mapsrv[$mapper]->label."? Try ";
$remaining=count($mapsrv)-2;
foreach ($mapsrv as $cursrv)
{
  if ($cursrv->token != $mapper)  {
    $_GET["map"] = $cursrv->token;
    $cururl=htmlentities($_SERVER["PHP_SELF"]."?".http_build_query($_GET));
    echo "<a href=\"".$cururl."\" class=\"block-link\" title=\"".$cursrv->label."\">".$cursrv->label."</a>";
    if ($remaining > 0) {
       echo " or ";
       $remaining--;
    }
  }
}
echo " instead)."
?>
</div>
</div>
<?php
// If the skin has post-content setup, include it here.
skin_include("block-map-post.php");
?>
