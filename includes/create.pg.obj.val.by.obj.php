<?php
/*
Copyright 2009-2023 Cargotrader, Inc. All rights reserved.

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

	echo $aoo('form');	#--1--

	$pg_obj_list = get_full_pg_obj_list();

	echo "<fieldset><legend>Page Objects (ID)</legend>\n";

	// Display all the object types.
	if (isset($pg_obj_list['obj_id']) && !empty($pg_obj_list['obj_id']) )
	{
		$obj_list_pos = (isset($_POST['obj_list']) && check_index($_POST['obj_list']) ) ? $_POST['obj_list'] : "";
		
		echo $aoo('select', "class=sel_css;\nname=obj_list;\ncore={$aoo('option', "value={$pg_obj_list['obj_id']};\ntval={$obj_list_pos};\nlabel={$pg_obj_list['obj_list_lbl']};\nlabelaft=&nbsp;")};\nonchange=clickB()");

		//"
		echo $aoo('button', "class=button_passive;\nname=selobj;\nvalue=1;\nlabel=Select Object;\nid=seltype");
		}
	else
	{
		echo $aoo('div', "class=divback;\ncore=<br>You Need to Create at Least One Page Object First! {$aoo('link', "style=font-weight: bold; font-style:italic;;\nhref=hoopla.fw.create.pg.objs.php;\ncore=Create Objects")}<br>&nbsp;");
		}

	echo "</fieldset>\n<br>\n";

	echo $aoo('hidden', "name=page;\nvalue=true");
	//--1-- "End of the first form
	echo "</form>\n";

//__________________________________________________________________________________________________________
	if (isset($_POST['page']) || isset($_POST['repage']) )
	{
		// Update setting changes
		if (isset($_POST['repage']) && isset($_POST['savesettings']) && $_POST['repage'] == 'true' && $_POST['savesettings'] == 'true' && isset($_POST['obj_list_cnt']) && $_POST['obj_list_cnt'] > 1)
		{
			$max = $_POST['obj_list_cnt'] - 1;

			// We need to block disconnecting a page from it's direct pg_obj
			$pg_tf = false;

			if (isset($_POST['obj_list']) && check_index($_POST['obj_list']) )
			{
				$pg_obj_id = $_POST['obj_list'];
				$sel_obj = sel_pg_obj($_POST['obj_list']);
				if ($sel_obj['sel_pg_obj_type_id'] == 14) {$pg_tf = true;}
				}
			else
				{$pg_obj_id = null;}

			// Go thru all the pages to see if there are any updates
			if (check_index($pg_obj_id) )
			{
				for ($j=1;$j <= $max;$j++)
				{
					if (isset($_POST['pg_id_hid'][$j]) && check_index($_POST['pg_id_hid'][$j]) )
					{
						$pg_id = $_POST['pg_id_hid'][$j];

						if (isset($_POST['spc_ord_tb'][$j]) && check_index($_POST['spc_ord_tb'][$j]) ) {$spc_ord = $_POST['spc_ord_tb'][$j];} else {$spc_ord = null;}
						if (isset($_POST['loc_tb'][$j]) ) {$obj_loc = $_POST['loc_tb'][$j];} else {$obj_loc = null;}
						if (isset($_POST['act_obj'][$j]) && check_index($_POST['act_obj'][$j]) && $_POST['act_obj'][$j] == $pg_id) 
							{$tf = true;} 
						else 
						{
							if ($pg_tf) {$tf = true;} 
							else 
							{
								$tf = false;
								unset($_POST['page']);
								unset($_POST['repage']);
								} 
							}

						if (isset($_POST['use_def_obj'][$j]) && check_index($_POST['use_def_obj'][$j]) && $_POST['use_def_obj'][$j] == $pg_id) 
							{$udb = true;} 
						else 
							{if ($pg_tf) {$udb = true;} else {$udb = false;} }
						
						$update = update_pg_pg_obj_brg($pg_id, $pg_obj_id, $obj_loc, $spc_ord, $tf, $udb);
						}
					}	# End of for loop
				}	# End of conditional
			}	# End of repage conditional
		else
		{
			include($sharedpath . 'upd.set.val.php');
			}
		clean_up_null_vals();
		}	# End of page-repage conditional
//__________________________________________________________________________________________________________
	// "Display values
	if ( (isset($_POST['page']) || isset($_POST['repage']) ) && isset($_POST['obj_list']) && check_index($_POST['obj_list']) )
	{
		// Get the pages associated with the given object
		$obj_pg_list = get_obj_pg_list($_POST['obj_list']);

		$sel_obj = sel_pg_obj($_POST['obj_list']);

		echo $aoo('form');	#--2--

		// Get the selected object type
		$sel_obj_name = $aoo('span', "core={$sel_obj['sel_obj_name']} ({$_POST['obj_list']}) &mdash; {$sel_obj['sel_obj_dsr']}");

		echo "<fieldset><legend>Pages Assigned to the Object: $sel_obj_name</legend>";

		$sel_obj_name_span = $aoo('span', "core={$sel_obj['sel_obj_name']}");

$tabletop = <<<TABLETOP
<table>
<caption>Select the page to enter or edit values.  Other options are saved below.  Unassigning a page will remove it from the list upon reload.</caption>
<thead>
<tr>
    <th>Active Page</th>
    <th>Page ID</th>
    <th>Page Name</th>
    <th>Page Description</th>
    <th>Access String</th>
    <th>Page Obj ID</th>
    <th>$sel_obj_name_span<br>Page Special Order</th>
    <th>$sel_obj_name_span<br>Page Location</th>
    <th>Allow<br>Defaults</th>
    <th>$sel_obj_name_span<br>Page Assignment</th>
    <th>Add/Edit Values</th>
</tr>
</thead>
<tbody>
TABLETOP;

		echo $tabletop;

		$i = 1;
		$colspan=11;
		$bc = row_color($i, null, "#DDD");


		if ($sel_obj['sel_pg_obj_type_id'] == 14) {$dis = "dis=1;\n";} else {$dis = "";}
		$def_vals = chk_for_def_vals($_POST['obj_list']);
		$def_val_count = count($def_vals);

		foreach ($obj_pg_list as $obj_pg_list_key=>$obj_pg)
		{
			extract($obj_pg);

			$pg_id_hid = $aoo('hidden', "name=pg_id_hid[$i];\nvalue=$pg_id");
	
			if ($pg_act_bit) {$enabled = "&#10004;";} else {$enabled = "";}
			
			$pg_name_div = $aoo('div', "class=name_border;\ncore=$pg_name");

			$spc_ord_tb = $aoo('textbox', "name=spc_ord_tb[$i];\nvalue=$ppob_spc_ord;\nmax=3;\nsize=5");

			$loc_tb = $aoo('textbox', "name=loc_tb[$i];\nvalue=$obj_loc;\nmax=255;\nsize=15");

			$use_def_obj = $aoo('cb', "name=use_def_obj[$i];\nvalue=$pg_id;\ntf=$ppob_use_def_bit");

			$act_obj = $aoo('cb', "{$dis}name=act_obj[$i];\nvalue=$pg_id;\ntf=$ppob_act_bit");

			if (isset($_POST['sel_pg'][$i]) && check_index($_POST['sel_pg'][$i]) && $_POST['sel_pg'][$i] == $pg_id) {$class = "class=button_active;\n";} else {$class="class=button_passive;\n";}
			$select = $aoo('button', "{$class}name=sel_pg[$i];\nvalue=$pg_id;\nlabel=Select");

			if ($pg_acs_str === NULL) {$pg_acs_str = '&nbsp;';}

			// We need to see if the pg obj values were created or updated by checking against $_POST vars
			if (isset($_POST['objvals']) && $_POST['objvals'] == 'true' && isset($_POST['obj_i']) && check_index($_POST['obj_i']) && isset($_POST['sel_pg']) && isset($_POST['sel_pg'][$i]) && $_POST['sel_pg'][$i] == $pg_id)
			{
				$upd_obj_id = (check_index($_POST['obj_list']) ) ? $_POST['obj_list'] : null;

				include ($sharedpath . 'upd.sets.php');
				
				// Need to check if there were any updates on default values
				$def_vals = chk_for_def_vals($_POST['obj_list']);
				}	# End of updating new or edited values

			// Get the existing object values
			$pg_obj_vals = get_vals_by_pg_and_obj($pg_id, $_POST['obj_list']);

$tabledata = <<<TABLEDATA
<tr>\n
    <td style="text-align:center; $bc" id="$pg_id" >&nbsp;$enabled&nbsp;</td>\n
    <td style="text-align:center; $bc">$pg_id{$pg_id_hid}</td>\n
    <td style="text-align:center; $bc">$pg_name_div</td>\n
    <td style="$bc">$pg_dsr</td>\n
    <td style="$bc">$pg_acs_str</td>\n
    <td style="text-align:center; $bc">$pg_obj_id</td>\n
    <td style="text-align:center; $bc">$spc_ord_tb</td>\n
    <td style="text-align:center; $bc">$loc_tb</td>\n
    <td style="text-align:center; $bc">$use_def_obj&nbsp;</td>\n
    <td style="text-align:center; $bc">$act_obj</td>\n
    <td style="text-align:center; $bc">$select</td>\n
 </tr>\n
TABLEDATA;

			echo $tabledata;

			$colspan=10;

			include($sharedpath . 'print.out.existing.obj.vals.php');

			$i++;
	       	}	# End of foreach

		// Case where there are no assigned pages (which shouldn't happen)
		if ($i == 1)
		{
$tabledata = <<<TABLEDATA
<tr>\n
    <td style="text-align:center; $bc" colspan="$colspan">Oops!  We cannot find any pages assigned to this object!</td>\n
 </tr>\n
TABLEDATA;

			echo $tabledata;
			}
		
		$colspan=11;
		
		include($sharedpath . 'print.out.existing.obj.defs.php');
		
		echo "</tbody>";
		echo "</table><br>\n";

		if ($i > 1)
		{
			echo $aoo('button', "class=button_passive;\nname=savesettings;\nvalue=true;\nlabel=Update Settings;\nfore=<br>\n");
			echo $aoo('reset', "class=button_passive");
			}
			
		echo "</fieldset>\n<br>\n";

		echo $aoo('hidden', "name=repage;\nvalue=true");
		echo $aoo('hidden', "name=obj_list;\nvalue={$_POST['obj_list']}");
		echo $aoo('hidden', "name=obj_list_cnt;\nvalue=$i");

		//--2-- End of the second form
		echo "</form>\n";

		// Select object setting type as filter____________________________________________________________
		if (isset($_POST['repage']) && $_POST['repage'] == 'true' && isset($_POST['sel_pg']) && isset($_POST['obj_list']) && check_index($_POST['obj_list']) )
		{
			// Get the selected page
			if (isset($_POST['sel_pg']) && count($_POST['sel_pg']) == 1)
				{$pg_id = current($_POST['sel_pg']); $sel_pg_key = key($_POST['sel_pg']);}

			$sel_obj_name_span = $sel_obj_name;

			$_POST['settype'] = 'true';

			// use the $sel_obj_id
			$sel_obj_id = $_POST['obj_list'];
		
			$sel_pg = get_pg_info($pg_id);
			$sel_pg_name = $sel_pg['sel_obj_name'];
			$sel_pg_obj_type_id = $sel_obj['sel_pg_obj_type_id'];
			
			include($sharedpath . 'obj.set.type.form.php');
			
			echo $aoo('hidden', "name=form3;\nvalue=true");
			echo $aoo('hidden', "name=repage;\nvalue=true");
			echo $aoo('hidden', "name=sel_pg[$sel_pg_key];\nvalue=$pg_id");
			echo $aoo('hidden', "name=obj_list;\nvalue={$sel_obj_id}");

			//--3-- End of the form
			echo "</form>\n";

			$sel_pg_name = (isset($sel_pg_name) ) ? $aoo('span', "core=$sel_pg_name") : null;

			// "Display/Add/Update object values________________________________________________________________
			if ( (isset($post_obj_sets) && check_index($post_obj_sets) ) && ( (isset($_POST['settype']) && $_POST['settype'] == 'true') || (isset($_POST['objvals']) && $_POST['objvals'] == 'true') ) )
			{
				echo $aoo('form');	#--4--

				include ($sharedpath . 'obj.vals.php');

				echo $aoo('hidden', "name=form4;\nvalue=true");
				echo $aoo('hidden', "name=repage;\nvalue=true");
				echo $aoo('hidden', "name=sel_pg[$sel_pg_key];\nvalue=$pg_id");
				echo $aoo('hidden', "name=obj_list;\nvalue={$sel_obj_id}");
				echo $aoo('hidden', "name=obj_i;\nvalue=$i");
				echo $aoo('hidden', "name=obj_sets;\nvalue={$post_obj_sets}");

				//--4-- End of the form
				echo "</form>\n";
				}	# End of Display/Add/Update object values
			} # End of repage
		} # End of original post conditional

?>

<script>
	var element = document.getElementById("openbox");
		if (element) {
	element.scrollIntoView();
	}
</script>
