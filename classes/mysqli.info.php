<?php

// DB connection info

$targetfile = "{$classpath}info/{$_SERVER['REMOTE_ADDR']}";

if (file_exists($targetfile) ) {include($targetfile);}
else {include("{$classpath}info/hfw.mysqli.info.php");}

?>
