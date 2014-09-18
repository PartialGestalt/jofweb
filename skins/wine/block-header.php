<?php
  /* The unclosed <div> is not an error here.  We're using the
   * red-bar-content container to handle our centering, so the
   * main nav area must be part of that DIV.  The tag is closed
   * by a skin-specific "post" file, included by the core block-nav.php
   * file.
   * The dangerous assumption here is that all pages that have a header
   * will also have the main nav immediately following (in the HTML, at
   * least, if not in page format).
   */
?>
<div id="red-bar-spacer"></div>
<div id="red-bar-content">
<div id="red-bar-logo"><a href="/" title="Journey of Faith home"><img class="frameless" src="/skins/wine/logo_header_white.png" alt="Journey of Faith home" style="padding-top:10px;"/></a></div>
