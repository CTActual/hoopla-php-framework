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

	$ctx_types = get_ctx_types();

	echo "<fieldset><legend>Context-Types (ID)</legend>";

	// List all the available context-types to choose from
	$post_ctx_types = (isset($_POST['ctx_types']) && check_index($_POST['ctx_types']) ) ? $_POST['ctx_types'] : null;
	echo $aoo('select', "class=sel_css;\nname=ctx_types;\ncore={$aoo('option', "value={$ctx_types['type_id']};\ntval={$post_ctx_types};\ndval=31;\nlabel={$ctx_types['opt_lbl']}")};\nonchange=clickB()");
	echo $aoo('button', "class=button_passive;\nname=seltype;\nvalue=1;\nlabel=Select Context Type;\nid=seltype");
	echo "</fieldset><br>\n";

	echo $aoo('hidden', "name=page;\nvalue=true");
	//--1-- End of first form
	echo "</form>";

//_______________________________________________________________________________________
	if (isset($_POST['page']) || isset($_POST['repage']) )
	{
		echo $goo('form');		#--2--

		$sel_ctx_type_id = (int) $_POST['ctx_types'];
		$sel_ctx_id = 0;
		$sel_ctx_name = "";
		$sel_ctx_dsr = "";
		$sel_ctx_lbl = "";
		$sel_ctx_spc_ord = "";

		if (isset($_POST['repage']) && isset($_POST['addnew']) )
		{
			$post_ctx_spc_ord = (isset($_POST['ctx_spc_ord']) && is_numeric($_POST['ctx_spc_ord']) ) ? $_POST['ctx_spc_ord'] : null;
			$post_ctx_dsr = (isset($_POST['ctx_dsr']) && !empty($_POST['ctx_dsr']) ) ? $_POST['ctx_dsr'] : null;
			
			$ins_value = create_ctx($_POST['ctx_name'], $_POST['ctx_lbl'], $post_ctx_dsr, $post_ctx_spc_ord, $sel_ctx_type_id);
			} 
		elseif (isset($_POST['repage']) && isset($_POST['updatectx']) )
		{
			$post_ctx_spc_ord = (isset($_POST['ctx_spc_ord']) && is_numeric($_POST['ctx_spc_ord']) ) ? $_POST['ctx_spc_ord'] : null;
			$post_ctx_dsr = (isset($_POST['ctx_dsr']) && !empty($_POST['ctx_dsr']) ) ? $_POST['ctx_dsr'] : null;

			$upd_value = update_ctx($_POST['update_ctx_id'], $_POST['ctx_name'], $_POST['ctx_lbl'], $post_ctx_dsr, $post_ctx_spc_ord);
			}
		elseif (isset($_POST['repage']) && isset($_POST['activebit']) )
		{
			# Go through all the special order boxes to see if anything has changed and make necessary updates
			if (isset($_POST['spc_ord_hid']) && is_array($_POST['spc_ord_hid']) )
			{
				foreach ($_POST['spc_ord_hid'] as $hid_ctx_id=>$hid_ctx_spc_ord)
				{
					if ($hid_ctx_spc_ord != 'null')
					{
						if (isset($_POST['spc_ord_box'][$hid_ctx_id]) )
						{
							if ($_POST['spc_ord_hid'][$hid_ctx_id] != $_POST['spc_ord_box'][$hid_ctx_id]) {$upd_spc_ord = update_ctx_spc_ord($hid_ctx_id, $_POST['spc_ord_box'][$hid_ctx_id]);}
							}
						else {$upd_spc_ord = update_ctx_spc_ord($hid_ctx_id);}
						}	# End of not-null spc_ord
					elseif (isset($_POST['spc_ord_box'][$hid_ctx_id]) )
						{$upd_spc_ord = update_ctx_spc_ord($hid_ctx_id, $_POST['spc_ord_box'][$hid_ctx_id]);}
					}	# End of spc_ord foreach
				}	# End of isset and is_array check

			# Go through all the active bit boxes to make any necessary updates
			if (isset($_POST['ctx_act_hid']) &&  is_array($_POST['ctx_act_hid']) )
			{
				foreach ($_POST['ctx_act_hid'] as $act_bit_key=>$bit_ctx_id)
				{
					if (isset($_POST['ctx_act'][$act_bit_key+1]) && $_POST['ctx_act'][$act_bit_key+1] == $bit_ctx_id)
						{$upd_act_bit = update_ctx_act_bit($bit_ctx_id);}
					else
						{$upd_act_bit = update_ctx_act_bit($bit_ctx_id, false);}
					}	# End of act_bit foreach
				}	# End of isset and is_array check
			}	# End of activebit and spc_ord updates

		if (isset($_POST['repage']) && isset($_POST['edit_ctx']) )
		{
			foreach ($_POST['edit_ctx'] as $edit_ctx_key=>$sel_ctx_id)
			{
				extract(get_ctx($sel_ctx_id), EXTR_PREFIX_ALL, 'sel');
				}
			} # end of repage conditional

//__________________________________________________________________________________________
		// Replace blanks with nulls
		$force_null = force_ctx_nulls();
//__________________________________________________________________________________________
		// Get the existing contexts of the given ctx-type
		$ctxes = get_ctx_by_type($_POST['ctx_types']);

		$type_info = get_type($sel_ctx_type_id);
		extract($type_info);

		// List all the existing roles of the given ctx-type
		$ctx_type_name = $aoo('span', "core=$type_name ($sel_ctx_type_id)");

		echo "<fieldset><legend>Contexts of Type: $ctx_type_name</legend>\n";

$tabletop = <<<TABLETOP
<table>
<thead>
<tr>
    <th style="text-align:center;" >Context ID</th>
    <th>Context Name</th>
    <th>Look-Up Label</th>
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

		foreach ($ctxes as $ctx_key=>$ctx)
		{
			$bc = row_color($i, null, "#DDD");

			extract($ctx);

			$ctx_spc_ord_box = $aoo('textbox', "name=spc_ord_box[$ctx_id];\nsize=3;\nmax=3;\nvalue=$ctx_spc_ord");
			if (empty($ctx_spc_ord) ) {$ctx_spc_ord = 'null';}
			$ctx_spc_ord_hid = $aoo('hidden', "name=spc_ord_hid[$ctx_id];\nvalue=$ctx_spc_ord");
			$ctx_bit = $aoo('cb', "name=ctx_act[$i];\nvalue=$ctx_id;\ntf=$ctx_act_bit");
			$ctx_bit_hid = $aoo('hidden', "name=ctx_act_hid[];\nvalue=$ctx_id");
			if (isset($sel_ctx_id) && $sel_ctx_id == $ctx_id) {$button_class = "class=button_active";} else {$button_class = "class=button_passive";}

			$edit_button = $aoo('button', "{$button_class};\nname=edit_ctx[];\nvalue=$ctx_id;\nlabel=Select");

$tabledata = <<<TABLEDATA
<tr>\n
    <td style="text-align:center;$bc ">$ctx_id</td>\n
    <td style="text-align:center;$bc ">$ctx_name</td>\n
    <td style="text-align:center;$bc ">$ctx_lbl</td>\n
    <td style="$bc ">$ctx_dsr</td>\n
    <td style="text-align:center;$bc ">$ctx_spc_ord_box$ctx_spc_ord_hid</td>\n
    <td style="text-align:center;$bc ">$ctx_bit$ctx_bit_hid</td>\n
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
    <td style="text-align:center;$bc " colspan="7">Create Your First $ctx_type_name Below!</td>\n
 </tr>\n
TABLEDATA;

			echo $tabledata;
			}

		echo "</tbody>";
		echo "</table><br>\n";

		//  Form for adding new role
		if ($i > 1) {echo $aoo('button', "class=button_passive;\nname=activebit;\nvalue=1;\nlabel=Update Active and Special Order Lists;\naft=<br>");}
		echo "</fieldset><br>";

		$ctx_type_name = $aoo('span', "core=$type_name ($sel_ctx_type_id)");
		$ctx_name_span = $aoo('span', "core=$ctx_name ($sel_ctx_id)");

		if (check_index($sel_ctx_id) )
		{
			echo "<fieldset><legend>Add/Update Context of Type: $ctx_type_name</legend>";

			$editing = "<br><br>Currently editing context $ctx_name_span. (You can still create a new context.)";
			}
		else
		{
			echo "<fieldset><legend>Add New Context of Type: $ctx_type_name</legend>";
			$editing = "";
			}

		echo $aoo('textbox', "name=ctx_name;\nsize=50;\nmax=50;\nvalue=$sel_ctx_name;\nlabel=&nbsp;Context Name;\naft=<br>");
		echo $aoo('textbox', "name=ctx_lbl;\nsize=25;\nmax=25;\nvalue=$sel_ctx_lbl;\nlabel=&nbsp;Context Look-Up Label;\naft=<br>");
		echo $aoo('textbox', "name=ctx_spc_ord;\nsize=5;\nmax=5;\nvalue=$sel_ctx_spc_ord;\nlabel=&nbsp;Context Special Order (Optional);\naft=<br>");
		echo $aoo('textarea', "name=ctx_dsr;\nrows=5;\ncols=50;\nvalue=$sel_ctx_dsr;\nlabel=<br>Context Description (Highly Desirable);\naft=<br><br>");

		echo $aoo('button', "class=button_passive;\nname=addnew;\nvalue=true;\nlabel=Add Context");
		if (check_index($sel_ctx_id) ) {echo $aoo('button', "class=button_passive;\nname=updatectx;\nvalue=true;\nlabel=Update Context");}
		echo $aoo('reset', "class=button_passive");
		echo $editing;
		echo "<br></fieldset><br>\n";

		echo $aoo('hidden', "name=repage;\nvalue=true");
		echo $aoo('hidden', "name=ctx_types;\nvalue={$_POST['ctx_types']}");
		echo $aoo('hidden', "name=update_ctx_id;\nvalue={$sel_ctx_id}");
		//--2-- End of form 2
		echo "</form>";
		} # End of original post conditional

?>
