	  <nav>
	    <div id="menubar">
          <ul id="nav">
<?php
/*
Copyright 2009-2022 Cargotrader, Inc. All rights reserved.

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

	$d = $commonpath . 'hoopla.fw.create.';
	$nav_array = array('Home', 'Pages', 'Objects', 'Assignment', 'Values-by-Page', 'Values-by-Object', 'Contexts', 'Types', 'Object-Type-Settings', 'Help');
	$href_array = array($commonpath . 'index.php', $d . 'pgs.php', $d . 'pg.objs.php', $d . 'pg.pg.obj.brg.php', $d . 'pg.obj.val.by.pg.php', $d . 'pg.obj.val.by.obj.php', $d . 'contexts.php', $d . 'types.php', $d . 'pg.obj.set.brg.php', $commonpath . 'hoopla.fw.help.php');
	$lis = array();

	foreach ($nav_array as $key=>$link)
	{
		if (stripos($_SERVER['PHP_SELF'], $href_array[$key]) !== false)
			{$cur_class = ";\nclass=current";}
		else
			{$cur_class = '';}

		$lis[] = "core={$aoo('link', "href={$href_array[$key]};\ncore=$link")}$cur_class";
		}

		echo $aoa('li', $lis);
?>
          </ul>
        </div><!--close menubar-->	
      </nav>

