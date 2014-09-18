<?php
$tfn=tempnam("/tmp","phptmp");
$tf=fopen($tfn,"w");
fwrite($tf,"lalalal");
fclose($tf);
?>
