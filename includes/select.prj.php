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

// We have processed the file change request before loading any classes

//____________________________________________________________________________________________________________________
// Process the change in db info
	if (isset($_REQUEST['db']) && $_REQUEST['db'] == 'true')
	{
		$dbname = null;
		$dblbl = null;
		$dbdsr = null;

		if (isset($_REQUEST['dbname']) ) {$dbname = $_REQUEST['dbname'];}
		if (isset($_REQUEST['dblbl']) ) {$dblbl = $_REQUEST['dblbl'];}
		if (isset($_REQUEST['dbdsr']) ) {$dbdsr = $_REQUEST['dbdsr'];}
		
		upd_db_info($dbname, $dblbl, $dbdsr);
		}
		
//____________________________________________________________________________________________________________________
	echo $aoo('form');	#--1--

	echo "<fieldset><legend>DB Connection File Listing in {$classpath}info (choose one to work on that project HFW database on this machine).</legend>";

$tabletop = <<<TABLETOP
<table>
<caption>Select File</caption>
<thead>
<tr>
    <th>File Name</th>
    <th>Choose</th>
</tr>
</thead>
<tbody>
TABLETOP;

			echo $tabletop;

			$i = 1;

	foreach ($dir as $key=>$file)
	{
		$bc = row_color($i, null, "#DDD");
		$label = "";
		$dis = "";
		$fw = "";
		
		if ( (empty($current) && $file == "hfw.mysqli.info.php") || (!empty($current) && $file == $current) )
		{
			$d = "default";
			if (!empty($current) && $file == "hfw.mysqli.info.php") {$d = "default &mdash; consider using a copy instead";}
			elseif (!empty($current) && $file == $current) {$d = "current";}
			
			$db_info = get_db_info();
			$label = "&nbsp;($d)<br>{$db_info['db_name']}&nbsp;({$db_info['db_lbl']})"; 
			$dis ="disabled=true;\n"; 
			$fw = "font-weight:bold; ";
			}

		$select = $aoo('button', "{$dis}class=button_passive;\nname=file[{$key}];\nvalue=true;\nlabel=Select");

$tabledata = <<<TABLEDATA
<tr>\n
    <td style="$fw$bc text-align:center;">$file$label</td>\n
    <td style="$bc text-align:center;">$select</td>\n
</tr>\n
TABLEDATA;

		echo $tabledata;
		$i++;
		}

	if ($i == 1)
	{
		$bc = row_color($i, null, "#DDD");

$tabledata = <<<TABLEDATA
<tr>\n
    <td colspan="2" style="$bc text-align:center; ">There are no DB connection files in the classes/info folder!</td>\n
</tr>\n
TABLEDATA;

		echo $tabledata;
		}

	echo "</tbody>";
	echo "</table><br>\n";
	echo "</fieldset></form><br>\n";

//____________________________________________________________________________________________________________________
	echo $aoo('form');	#--2--

	echo "<fieldset><legend>Update Project Info</legend>";
	
	if (isset($db_info) && is_array($db_info) && isset($db_info['db_name']) && isset($db_info['db_lbl']) && isset($db_info['db_dsr']) )
	{
		$db_name = $hed($db_info['db_name']);
		$db_lbl = $hed($db_info['db_lbl']);
		$db_dsr = $hed($db_info['db_dsr']);
		}
		
	echo $aoo('textbox', "name=dbname;\nsize=50;\nmax=63;\nvalue=$db_name;\nlabel=&nbsp;Project DB Name (Unique and Required);\naft=<br>");
	echo $aoo('textbox', "name=dblbl;\nsize=50;\nmax=63;\nvalue=$db_lbl;\nlabel=&nbsp;Project Name (Recommended);\naft=<br>");
	echo $aoo('textbox', "name=dbdsr;\nsize=50;\nmax=512;\nvalue=$db_dsr;\nlabel=&nbsp;Project Description (Not Required);\naft=<br>");
	echo $aoo('button', "fore=<br>;\nclass=button_passive;\nname=db;\nvalue=true;\nlabel=Update Info");
	echo $aoo('reset', "class=button_passive");

	echo "</fieldset></form><br>\n";

?>
<script>
	document.getElementById("myproject").innerHTML = "Current: <?php echo $gdbl(); ?>";
</script>