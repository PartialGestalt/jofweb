<div id="map-div-yahoo">
<div class="embedded-map" id="map-div-yahoo-embed"></div>
<?php
$yurl=htmlentities("http://api.maps.yahoo.com/ajaxymap?v=3.8&appid=lGTCDzPV34EfbUFG16mcITHnJK.jVKiq8gxWho9zYj8b.hWP2eXBDQukuQ--");
echo "<script type=\"text/javascript\" src=\"".$yurl."\"></script>";
?>
<script type="text/javascript">
var yloc=new YGeoPoint(30.560,-97.604);
var ymap=new YMap(document.getElementById('map-div-yahoo-embed'));
var jofmark=new YMarker(yloc);
ymap.addTypeControl();
ymap.addZoomLong();
ymap.setMapType(YAHOO_MAP_REG);
ymap.drawZoomAndCenter(yloc,4);
jofmark.addAutoExpand("Journey of Faith");
ymap.addOverlay(jofmark);
</script>
</div>
