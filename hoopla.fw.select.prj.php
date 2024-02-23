<?php
/*
Copyright 2009-2024 Cargotrader, Inc. All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are
permitted provided that the following conditions are met:

   1. Redistributions of source code must retain the above copyright notice, this list of
      conditions and the following disclaimer.

   2. Redistributions in binary form must reproduce the above copyright notice, this list
      of conditions and the following disclaimer in the documentation and/or other materials
      provided with the distribution.

THIS SOFTWARE IS PROVIDED BY Cargotrader, Inc. ''AS IS'' AND ANY EXPRESS OR IMPLIED
WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL Cargotrader, Inc. OR
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

The views and conclusions contained in the software and documentation are those of the
authors and should not be interpreted as representing official policies, either expressed
or implied, of Cargotrader, Inc.
*/

// Select a project to work on and update project info.

require_once('hoopla.fw.rel.path.php');

//____________________________________________________________________________________________________________________
	// Process any file change request first
	exec("ls \"{$classpath}info\" | grep php", $dir);
	$client = $_SERVER['REMOTE_ADDR'];
	$current = null;
	$db_info = array();
	$db_name = null;
	$db_lbl = null;
	$db_dsr = null;
	
	foreach ($dir as $key=>$file)
	{
		if (isset($_REQUEST['file']) && file_exists("{$classpath}info/{$file}") && isset($_REQUEST['file'][$key]) && $_REQUEST['file'][$key] == 'true')
		{
			copy("{$classpath}info/{$file}", "{$classpath}info/{$client}");
			file_put_contents("{$classpath}info/{$client}.txt", $file);
			}
		}

	if (file_exists("{$classpath}info/{$client}.txt") )
		{$current = file_get_contents("{$classpath}info/{$client}.txt");}
//____________________________________________________________________________________________________________________
		
require_once($incpath . 'common.incs.php');

?>
<!DOCTYPE html> 
<html>

<?php
	$title = "Hoopla Select Project";
	include($incpath . "std.header.php");
?>

<body>
  <div id="main">		

    <header>

<?php
	include($incpath . "std.nav.php");
	$banner = "The Hoopla PHP Framework";
	$sub_banner = "Select a Project";
	include($incpath . "std.banner.php");
?>
    </header>
    
	<div id="site_content">		
	
<?php
	include($sidepath . "std.sidebar.top.php");
	include($sidepath . "select.prj.sidebar.php");
	include($sidepath . "std.sidebar.bot.php");
?>

	  <div id="content">
        <div class="content_item">

<?php

	include($incpath . "select.prj.php");

?>	  
		</div><!--close content_item-->
      </div><!--close content-->   
	</div><!--close site_content-->  	

<?php
	include($incpath . "std.footer.php");
?>

  </div><!--close main-->
  
</body>
</html>
