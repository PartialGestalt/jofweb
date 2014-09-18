/*
 * default.css.h -- Common CSS for all skins
 *
 * WARNING: Do _not_ get specific here, this is really
 * only for macros, spacers, etc.
 *
 */

/* Shorthand for no padding, border, or margin */
#define PACKED() padding:0px;margin:0px;border:0px

/* Div to force closure of a container div with floated children */
.closure-div, #closure-div, .content-closure-div, #content-closure-div {
display:block;
clear:left;
line-height:0px;
height:0px;
}

/* Some common styling things for inheriting; these are usually what
 * skins will use, but may be overridden....
 */
ul {
list-style-type:none;
list-style-image:none;
}

a {
text-decoration:none;
}

/* Additional custom styles that are sufficiently generic */
.frameless {
PACKED();
}

.image-caption {
display:block;
text-align:center;
}
