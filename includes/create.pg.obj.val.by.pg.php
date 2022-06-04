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
		$show_obj_filter = true;

		require_once($sharedpath . 'pgs.form1.php');

// ______________________________________________________________________________________________
	if (isset($_POST['page']) || isset($_POST['repage']) )
	{
		if (isset($_POST['pg_id']) ) {$pg_id = (int) $_POST['pg_id'];} else {$pg_id = 0;}

		require_once($sharedpath . 'pgs.repage.php');

		// ____________________________________________________________________________

		// Get all the objects
		$pg_obj_list = get_pg_objs($pg_id, $post_obj_filter);
		extract(get_pg_info($pg_id) );
		$sel_pg_name = $aoo('span', "core=$sel_obj_name ($pg_id) [$sel_pg_ctx_lbl] &mdash; $sel_obj_dsr");

		// _________________________________________________________________________

		echo $aoo('form', "action=#type");	#--2--

		// Get the selected object type
		echo "<fieldset><legend>Objects for Page $sel_pg_name</legend>\n";

$tabletop = <<<TABLETOP
<table>
<caption>Use the Select Button to Enter or Edit Values. Other changes are saved below.</caption>
<thead>
<tr>
    <th style="text-align:center;" >Assigned</th>
    <th style="text-align:center;" >Allow<br>Defaults</th>
    <th>Object Type</th>
    <th>Object Name (id)</th>
    <th>Object Location on<br>Template (not value)</th>
    <th>Object Description</th>
    <th>Access String</th>
    <th style="text-align:center;" >Special Order</th>
    <th style="text-align:center;" >Object Enabled Globally</th>
    <th style="text-align:center;" >Add/Edit Values</th>
</tr>
</thead>
<tbody>
TABLETOP;

		echo $tabletop;

		$i = 1;
		$obj_count = 0;
		$colspan=10;

		foreach($pg_obj_list as $pg_obj_key=>$pg_obj)
		{
			extract($pg_obj);

			$bc = row_color($i, null, "#DDD");
	
			if ($pg_obj_act_bit) {$enabled = "&#10004;";} else {$enabled = "";}

			$act_obj = $aoo('cb', "name=act_obj[];\nvalue=$pg_obj_id;\ntf=$pg_obj_brg_act_bit");
			if ($pg_obj_brg_act_bit) {$obj_count++;}
			
			$use_def_obj = $aoo('cb', "name=use_def_obj[];\nvalue=$pg_obj_id;\ntf=$pg_obj_use_def_bit");

			$pg_obj_name_div = $aoo('div', "class=name_border;\ncore=$pg_obj_name ($pg_obj_id)");

			$obj_loc = $aoo('textbox', "name=obj_loc_{$pg_obj_id};\nsize=15;\nmax=30;\nvalue=$pg_obj_loc");

			$obj_spc_ord = $aoo('textbox', "name=obj_spc_ord_{$pg_obj_id};\nsize=3;\nmax=3;\nvalue=$pg_obj_spc_ord");

			if (isset($_POST['sel_obj'][$i]) && check_index($_POST['sel_obj'][$i]) && $_POST['sel_obj'][$i] == $pg_obj_id) {$class = "class=button_active;\n";} else {$class="class=button_passive;\n";}
			$select = $aoo('button', "{$class}name=sel_obj[$i];\nvalue=$pg_obj_id;\nlabel=Select");

			// We need to see if the pg values were created or updated by checking against $_POST vars
			if (isset($_POST['objvals']) && $_POST['objvals'] == 'true' && isset($_POST['obj_i']) && check_index($_POST['obj_i']) && isset($_POST['sel_obj']) && isset($_POST['sel_obj'][$i]) && $_POST['sel_obj'][$i] == $pg_obj_id)
			{
				$upd_obj_id = $pg_obj_id;

				include ($sharedpath . 'upd.sets.php');
				}	# End of updating new or edited values

			// Get the existing object values
			$pg_obj_vals = get_vals_by_pg_and_obj($pg_id, $pg_obj_id);
			$def_vals = chk_for_def_vals($pg_obj_id);

$tabledata = <<<TABLEDATA
<tr>\n
    <td style="text-align:center;$bc " id="$pg_obj_id">$act_obj&nbsp;</td>\n
    <td style="text-align:center;$bc ">$use_def_obj&nbsp;</td>\n
    <td style="$bc ">$pg_obj_type_name</td>\n
    <td style="$bc ">$pg_obj_name_div</td>\n
    <td style="$bc ">$obj_loc</td>\n
    <td style="$bc ">$pg_obj_dsr</td>\n
    <td style="$bc ">$pg_obj_acs_str</td>\n
    <td style="text-align:center;$bc ">$obj_spc_ord</td>\n
    <td style="text-align:center;$bc ">$enabled</td>\n
    <td style="text-align:center;$bc ">$select</td>\n
 </tr>\n
TABLEDATA;

			echo $tabledata;

			$colspan=9;

			include ($sharedpath . 'print.out.existing.obj.vals.php');
			$colspan=10;
			include($sharedpath . 'print.out.existing.obj.defs.php');

			$i++;
	       	}	# End of the object loop

		// No objects exist case
		if ($i == 1)
		{
			$bc = row_color($i, null, "#DDD");
	
$tabledata = <<<TABLEDATA
<tr>\n
    <td colspan="$colspan" style="text-align:center; $bc ">You Need To Create Some Objects First So You Can Assign Them to This Page!</td>\n
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
		echo "</fieldset><br>";
		echo $aoo('hidden', "name=repage;\nvalue=true");
		echo $aoo('hidden', "name=pg_id;\nvalue={$_POST['pg_id']}");
		echo $aoo('hidden', "name=obj_filter;\nvalue={$post_obj_filter}");

		//--2-- End of the second form
		echo "</form>";

		// Save values____________________________________________________________________________
		if (isset($_POST['repage']) && $_POST['repage'] == 'true')
		{
			if (isset($_POST['sel_obj']) && count($_POST['sel_obj']) == 1)
			{
				foreach ($_POST['sel_obj'] as $sel_obj_key=>$sel_obj_id)
				{
					extract(sel_pg_obj($sel_obj_id) );
					}
				$set_type_list = list_pg_obj_set_types($sel_pg_obj_type_id);
				}
			}

		// Select object setting type as filter____________________________________________________________
		if (isset($_POST['repage']) && $_POST['repage'] == 'true' && isset($_POST['sel_obj']) )
		{
			echo $aoo('form');	#--3--
			
			$def_vals = chk_for_def_vals($sel_obj_id);
			$def_val_count = count($def_vals);
			$set_type_count = det_num_of_set_types_per_obj($sel_obj_id, $pg_id);
			$use_def_bit = det_use_def_bit($sel_obj_id, $pg_id);

			# if there is only one setting type in use we default to that, unless the user selects a new one.
			if ( ($set_type_count == 1 || $def_val_count == 1) && !isset($_POST['settype']) )
			{
				// Get the existing object values
				$pg_obj_vals = get_vals_by_pg_and_obj($pg_id, $sel_obj_id);
				$_POST['settype'] = 'true';

				// Get the setting type of the selected object
				$_POST['obj_sets'] = ($set_type_count == 1) ? $pg_obj_vals[0]['val_pg_obj_set_type_id'] : $def_vals[0]['def_type_id'];
				}

			$sel_obj_name_span = $aoo('span', "core=$sel_obj_name ($sel_obj_id)");
			echo "<fieldset id=type><legend>Select the Setting Type for the Object: $sel_obj_name_span</legend>\n";
			$post_obj_sets = (isset($_POST['obj_sets']) && check_index($_POST['obj_sets']) ) ? $_POST['obj_sets'] : null;
			echo $aoo('select', "class=sel_css;\nname=obj_sets;\ncore={$aoo('option', "value={$set_type_list['pg_obj_set_type_id']};\ntval={$post_obj_sets};\nlabel={$set_type_list['set_type_list']};\nlabelaft=&nbsp;")};\nonchange=clickAny('settingtype')");

			echo $aoo('button', "class=button_passive;\nname=settype;\nvalue=true;\nlabel=Select Type;\nid=settingtype");
			echo "</fieldset><br>";

			echo $aoo('hidden', "name=form3;\nvalue=true");
			echo $aoo('hidden', "name=repage;\nvalue=true");
			echo $aoo('hidden', "name=sel_obj[$sel_obj_key];\nvalue=$sel_obj_id");
			echo $aoo('hidden', "name=pg_id;\nvalue={$_POST['pg_id']}");
			echo $aoo('hidden', "name=obj_filter;\nvalue={$post_obj_filter}");

			//--3-- End of the form
			echo "</form>";
			}

		// Deal with new object values________________________________________________________________
		if ( (isset($_POST['settype']) && $_POST['settype'] == 'true') || (isset($_POST['objvals']) && $_POST['objvals'] == 'true') )
		{
			echo $aoo('form', "action=#$sel_obj_id");	#--4--

			include ($sharedpath . 'obj.vals.php');

			echo $aoo('hidden', "name=form4;\nvalue=true");
			echo $aoo('hidden', "name=repage;\nvalue=true");
			echo $aoo('hidden', "name=sel_obj[$sel_obj_key];\nvalue=$sel_obj_id");
			echo $aoo('hidden', "name=obj_sets;\nvalue={$_POST['obj_sets']}");
			echo $aoo('hidden', "name=pg_id;\nvalue={$_POST['pg_id']}");
			echo $aoo('hidden', "name=obj_i;\nvalue=$i");
			echo $aoo('hidden', "name=obj_filter;\nvalue={$post_obj_filter}");

			//--4-- End of the form
			echo "</form>";
			}
		} # End of original post conditional

?>
