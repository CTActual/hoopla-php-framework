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

	echo $goo('form');	#--1--

	$meta_types = get_meta_type_list();

	echo "<fieldset><legend>Meta-Types (ID)</legend>";

	// List all the available meta-types to choose from
	$post_meta_types = (isset($_POST['meta_types']) && check_index($_POST['meta_types']) ) ? $_POST['meta_types'] : null;
	echo $aoo('select', "class=sel_css;\nname=meta_types;\ncore={$aoo('option', "value={$meta_types['meta_type_id']};\ntval={$post_meta_types};\nlabel={$meta_types['meta_type_lbl']}")};\nonchange=clickB()");
	echo $aoo('button', "class=button_passive;\nname=selmetatype;\nvalue=1;\nlabel=Select Meta Type;\nid=seltype");
	echo "</fieldset><br>\n";

	echo $aoo('hidden', "name=page;\nvalue=true");
	//--1-- End of first form
	echo "</form>";

//_______________________________________________________________________________________
	if (isset($_POST['page']) || isset($_POST['repage']) )
	{
		echo $goo('form');	#--2--

		$set_meta_type_id = (int) $_POST['meta_types'];
		$sel_type_id = 0;
		$sel_type_name = "";
		$sel_std_type_lbl = "";
		$sel_type_spc_ord = "";
		$sel_type_dsr = "";
		$sel_spc_ord = "";
		$post_type_name = (isset($_POST['type_name']) ) ? $_POST['type_name'] : null;
		$post_std_type_lbl = (isset($_POST['std_type_lbl']) ) ? $_POST['std_type_lbl'] : null;
		$post_type_dsr = (isset($_POST['type_dsr']) ) ? $_POST['type_dsr'] : null;
		$post_type_spc_ord = (isset($_POST['type_spc_ord']) ) ? $_POST['type_spc_ord'] : null;

		if (isset($_POST['repage']) && isset($_POST['addnew']) )
		{
			// Create a new type
			$new_type = create_type($post_type_name, $set_meta_type_id, $post_std_type_lbl, $post_type_dsr, $post_type_spc_ord);
			} 
		elseif (isset($_POST['repage']) && isset($_POST['updatetype']) && isset($_POST['update_type_id']) && isset($post_type_name) )
		{
			// Update the selected type
			$updated_type = update_type($_POST['update_type_id'], $post_type_name, $post_std_type_lbl, $post_type_dsr, $post_type_spc_ord);
			}
		elseif (isset($_POST['repage']) && isset($_POST['activebit']) )
		{
			// Go through all the special order boxes to see if anything has changed and make necessary updates
			if (isset($_POST['spc_ord_hid']) && is_array($_POST['spc_ord_hid']) )
			{
				foreach ($_POST['spc_ord_hid'] as $hid_type_id=>$hid_type_spc_ord)
				{
					if ($hid_type_spc_ord != 'null')
					{
						if (isset($_POST['spc_ord_box'][$hid_type_id]) )
						{
							if ($_POST['spc_ord_hid'][$hid_type_id] != $_POST['spc_ord_box'][$hid_type_id]) {$upd_spc_ord = update_type_spc_ord($hid_type_id, $_POST['spc_ord_box'][$hid_type_id]);}
							}
						else {$upd_spc_ord = update_type_spc_ord($hid_type_id);}
						}	# End of not-null spc_ord
					elseif (isset($_POST['spc_ord_box'][$hid_type_id]) )
						{$upd_spc_ord = update_type_spc_ord($hid_type_id, $_POST['spc_ord_box'][$hid_type_id]);}
					}	# End of spc_ord foreach
				}
				
			// Go through all the active bit boxes to make any necessary updates
			if (isset($_POST['type_act_hid']) && is_array($_POST['type_act_hid']) )
			{
				foreach ($_POST['type_act_hid'] as $act_bit_key=>$bit_type_id)
				{
					if (isset($_POST['type_act'][$act_bit_key+1]) && $_POST['type_act'][$act_bit_key+1] == $bit_type_id)
						{$upd_act_bit = update_type_act_bit($bit_type_id);}
					else
						{$upd_act_bit = update_type_act_bit($bit_type_id, false);}
					}	# End of act_bit foreach
				}
			}	# End of activebit conditional
		elseif (isset($_POST['repage']) )
		{
			if (isset($_POST['edit_type']) && is_array($_POST['edit_type']) )
			{
				foreach ($_POST['edit_type'] as $edit_type_key=>$sel_type_id)
				{
					extract(get_type($sel_type_id), EXTR_PREFIX_ALL, 'sel');
					}
				}
			} # end of repage conditional

//__________________________________________________________________________________________
		// Enforce type null values
		$enforce_nulls = enforce_type_nulls();

//__________________________________________________________________________________________
		// Get the existing types of the given meta-type
		$meta_type = get_meta_type($_POST['meta_types']);
		extract($meta_type);
		$type_list = get_types($_POST['meta_types']);

		// List all the existing types of the given meta-type
		$mt_name_span = $aoo('span', "core=$meta_type_name ({$_POST['meta_types']})");

		echo "<fieldset><legend>Types of Meta-Type: $mt_name_span</legend>\n";

$tabletop = <<<TABLETOP
<table>
<thead>
<tr>
    <th style="text-align:center;" >Type ID</th>
    <th>Type Name</th>
    <th>Standard Type Label</th>
    <th>Description</th>
    <th style="text-align:center;" >Special Order</th>
    <th style="text-align:center;" >Active</th>
    <th style="text-align:center;" >Edit</th>
</tr>
</thead>
<tbody>
TABLETOP;

		echo $tabletop;

		$i = 1;

		foreach ($type_list as $types_key=>$type)
		{
			extract($type);

			$bc = row_color($i, null, "#DDD");

			$type_spc_ord_box = $aoo('textbox', "name=spc_ord_box[$type_id];\nsize=3;\nmax=3;\nvalue=$type_spc_ord");
			if (empty($type_spc_ord) ) {$type_spc_ord = 'null';}
			$type_spc_ord_hid = $aoo('hidden', "name=spc_ord_hid[$type_id];\nvalue=$type_spc_ord");
			$type_bit = $aoo('cb', "name=type_act[$i];\nvalue=$type_id;\ntf=$type_act_bit");
			$type_bit_hid = $aoo('hidden', "name=type_act_hid[];\nvalue=$type_id");

			if ($type_id == $sel_type_id) {$button_class = "class=button_active;\n";} else {$button_class = "class=button_passive;\n";}
			$edit_button = $aoo('button', "{$button_class}name=edit_type[];\nvalue=$type_id;\nlabel=Select");

$tabledata = <<<TABLEDATA
<tr>\n
    <td style="text-align:center;$bc ">$type_id</td>\n
    <td style="$bc ">$type_name</td>\n
    <td style="$bc ">$std_type_lbl</td>\n
    <td style="$bc ">$type_dsr</td>\n
    <td style="text-align:center;$bc ">$type_spc_ord_box$type_spc_ord_hid</td>\n
    <td style="text-align:center;$bc ">$type_bit$type_bit_hid</td>\n
    <td style="text-align:center;$bc ">$edit_button</td>\n
 </tr>\n
TABLEDATA;

			echo $tabledata;
			$i++;
	       		}

		# No results case
		if ($i == 1)
		{
			$bc = row_color($i, null, "#DDD");

$tabledata = <<<TABLEDATA
<tr>\n
    <td style="text-align:center;$bc " colspan="7">Create Your First New $mt_name_span Below!</td>\n
 </tr>\n
TABLEDATA;

			echo $tabledata;
			}

		//  Form for adding/editing new type
		echo "</tbody>";
		echo "</table><br>\n";

		if ($i > 1) {echo $aoo('button', "class=button_passive;\nname=activebit;\nvalue=1;\nlabel=Update Active and Special Order Lists");}
		echo "</fieldset><br>\n";

		if (isset($sel_type_id) && check_index($sel_type_id) ) {$add_upd = "/Update";} else {$add_upd = "";}
		$mt_name_span = $aoo('span', "core=$meta_type_name ($set_meta_type_id)");

		echo "<fieldset><legend>Add$add_upd Type of Meta-Type: $mt_name_span</legend>\n";

		echo $aoo('textbox', "name=type_name;\nsize=50;\nmax=255;\nvalue=$sel_type_name;\nlabel=&nbsp;Type Name;\naft=<br>");
		echo $aoo('textbox', "name=std_type_lbl;\nsize=50;\nmax=63;\nvalue=$sel_std_type_lbl;\nlabel=&nbsp;Std Type Label;\naft=<br>");
		echo $aoo('textbox', "name=type_spc_ord;\nsize=3;\nmax=3;\nvalue=$sel_type_spc_ord;\nlabel=&nbsp;Special Ordering;\naft=<br>");
		echo $aoo('textarea', "name=type_dsr;\nrows=5;\nvalue=$sel_type_dsr;\nlabel=<br>Type Description (Highly Desirable);\naft=<br><br>");

		echo $aoo('button', "class=button_passive;\nname=addnew;\nvalue=true;\nlabel=Add Type");
		if (isset($sel_type_id) && check_index($sel_type_id) ) {echo $aoo('button', "class=button_passive;\nname=updatetype;\nvalue=true;\nlabel=Update Type");}

		echo $aoo('reset', "class=button_passive");

		$sel_name_span = $aoo('span', "core=$sel_type_name ($sel_type_id)");
		if (isset($sel_type_id) && check_index($sel_type_id) ) {echo "<br><br>Currently editing type $sel_name_span. (You can still create a new type using a different name and label.)\n";}

		echo "</fieldset><br>\n";

		echo $aoo('hidden', "name=repage;\nvalue=true");
		echo $aoo('hidden', "name=meta_types;\nvalue=$set_meta_type_id");
		echo $aoo('hidden', "name=update_type_id;\nvalue=$sel_type_id");
		//--2-- End of form 2
		echo "</form>";
		} # End of original post conditional

?>
