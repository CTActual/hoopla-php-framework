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

		require_once($sharedpath . 'pgs.form1.php');

// ______________________________________________________________________________________________
	if (isset($_POST['page']) || isset($_POST['repage']) )
	{
		if (isset($_POST['pg_id']) ) {$pg_id = (int) $_POST['pg_id'];} else {$pg_id = 0;}

		require_once($sharedpath . 'pgs.repage.php');

		// ____________________________________________________________________________

		// Get all the objects
		$pg_obj_list = get_pg_objs($pg_id);
		extract(get_pg_info($pg_id) );
		$sel_pg_name = $aoo('span', "core=$sel_obj_name ($pg_id) [$sel_pg_ctx_lbl] &mdash; $sel_obj_dsr");

		// _________________________________________________________________________

		echo $goo('form');	#--2--

		// Get the selected object type
		echo "<fieldset><legend>Objects for Page $sel_pg_name</legend>\n";

$tabletop = <<<TABLETOP
<table>
<caption>Select the necessary objects and enter locations</caption>
<thead>
<tr>
    <th style="text-align:center;" >Assigned</th>
    <th style="text-align:center;" >Fallbacks<br>PG|CTX</th>
    <th>Object Type</th>
    <th>Object Name (id)</th>
    <th>Object Location on Template (not value)</th>
    <th>Object Description</th>
    <th>Access String</th>
    <th style="text-align:center;" >Special Order Globally</th>
    <th style="text-align:center;" >Object Enabled</th>
</tr>
</thead>
<tbody>
TABLETOP;

		echo $tabletop;

		$i = 1;
		$obj_count = 0;

		foreach($pg_obj_list as $pg_obj_key=>$pg_obj)
		{
			extract($pg_obj);

			$bc = row_color($i, null, "#DDD");
	
			if ($pg_obj_act_bit) {$enabled = "&#10004;";} else {$enabled = "";}

			$act_obj = $aoo('cb', "name=act_obj[];\nvalue=$pg_obj_id;\ntf=$pg_obj_brg_act_bit");
			if ($pg_obj_brg_act_bit) {$obj_count++;}

			$use_def_obj = $aoo('cb', "name=use_def_obj[];\nvalue=$pg_obj_id;\ntf=$pg_obj_use_def_bit");
			$udo_div = $aoo('span', "style=text-align:center;;\ncore=$use_def_obj");

			$use_def_ctx_obj = $aoo('cb', "name=use_def_ctx_obj[];\nvalue=$pg_obj_id;\ntf=$pg_obj_use_def_ctx_bit");
			$udco_div = $aoo('span', "style=text-align:center;\ncore=$use_def_ctx_obj");

			$obj_loc = $aoo('textbox', "name=obj_loc_{$pg_obj_id};\nsize=30;\nmax=30;\nvalue=$pg_obj_loc");

			$obj_spc_ord = $aoo('textbox', "name=obj_spc_ord_{$pg_obj_id};\nsize=3;\nmax=3;\nvalue=$pg_obj_spc_ord");

$tabledata = <<<TABLEDATA
<tr>\n
    <td style="text-align:center;$bc ">$act_obj</td>\n
    <td style="text-align:center;$bc ">$udo_div$udco_div</td>\n
    <td style="$bc ">$pg_obj_type_name</td>\n
    <td style="$bc ">$pg_obj_name ($pg_obj_id)</td>\n
    <td style="$bc ">$obj_loc</td>\n
    <td style="$bc ">$pg_obj_dsr</td>\n
    <td style="$bc ">$pg_obj_acs_str</td>\n
    <td style="text-align:center;$bc ">$obj_spc_ord</td>\n
    <td style="text-align:center;$bc ">$enabled</td>\n
 </tr>\n
TABLEDATA;

			echo $tabledata;
			$i++;

			// Copy object to the blank page if desired
			if (isset($_POST['blanks']) && check_index($_POST['blanks']) && isset($_POST['repage']) && isset($_POST['copy']) && $_POST['copy'] == 'true' && $pg_obj_brg_act_bit !== NULL && $pg_obj_brg_act_bit && $pg_obj_act_bit)
			{
				$copy_id = copy_pg_objs($_POST['blanks'], (int) $pg_obj_id, $pg_obj_loc, (int) $pg_obj_spc_ord);
				}
	       	}	# End of the object loop

		// No objects exist case
		if ($i == 1)
		{
			$bc = row_color($i, null, "#DDD");
	
$tabledata = <<<TABLEDATA
<tr>\n
    <td colspan="9" style="text-align:center; $bc ">You Need To Create Some Objects First So You Can Assign Them to This Page!</td>\n
 </tr>\n
TABLEDATA;

			echo $tabledata;
			}
		
		echo "</tbody>";
		echo "</table><br>\n";
		if ($i > 1)
		{
			echo $aoo('button', "class=button_passive;\nname=save;\nvalue=true;\nlabel=Save/Update Object List");
			echo $aoo('reset', "class=button_passive;\naft=<br>");
			}
		echo "<br></fieldset><br>";

		// Deal with copying page to blank page________________________________________________________________
		$obj_less_pgs = get_blank_pgs();

		if (!empty($obj_less_pgs['blank_pg_ids']) && $obj_count > 0)
		{
			echo "<fieldset><legend>Copy Objects from the Page $sel_pg_name To The Selected Page with No Objects Assigned</legend>\n";
			echo $aoo('span', "style=font-size:16px;;\ncore=Source Page: $source_pg;\nfore=<br>;\naft=<br><br>");
			echo $aoo('span', "style=font-size:16px;;\ncore=Destination Page: ");
			echo $aoo('select', "class=sel_css;\nname=blanks;\ncore={$aoo('option', "labelfore=&nbsp;;\nlabelaft=&nbsp;;\nstyle=font-weight:bold; font-size:16px;;\nvalue={$obj_less_pgs['blank_pg_ids']};\nlabel={$obj_less_pgs['blank_pg_names']}")}");

			echo $aoo('button', "class=button_passive;\nfore=<br><br>;\nname=copy;\nvalue=true;\nlabel=Copy Objects From Source To Destination Page");
			echo "<br></fieldset><br>";
			}

		echo $aoo('hidden', "name=repage;\nvalue=true");
		echo $aoo('hidden', "name=pg_id;\nvalue={$_POST['pg_id']}");

		//"--2-- End of the second form
		echo "</form>";
		} # End of original post conditional

?>
