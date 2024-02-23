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

	// Create page objects

	echo $goo('form');	#--1--

	$pg_obj_type_list = pg_obj_type_list();

	echo "<fieldset><legend>Page Object Types [shorthand] (Type ID)</legend>";

	// Show all object types
	$post_obj_type_id = (isset($_POST['obj_type_id']) && check_index($_POST['obj_type_id']) ) ? $_POST['obj_type_id'] : null;
	echo $aoo('select', "class=sel_css;\nname=obj_type_id;\ncore={$aoo('option', "value={$pg_obj_type_list['type_id']};\ntval={$post_obj_type_id};\ncore={$pg_obj_type_list['type_lbl']};\ncoreaft=&nbsp;")};\nonchange=clickB()");

	echo $aoo('button', "class=button_passive;\nname=seltype;\nvalue=1;\nlabel=Select Object Type;\nid=seltype");

	echo "</fieldset><br>\n";

	echo $aoo('hidden', "name=page;\nvalue=true");

	//"--1-- End of first form
	echo "</form>";

//" _________________________________________________________________________________________
	if (isset($_POST['page']) || isset($_POST['repage']) )
	{
		// Clean up empty string records
		$null_output = clean_up_pg_obj_nulls();

		echo $goo('form');	#--2--

		$obj_type_id = (isset($_POST['obj_type_id']) && check_index($_POST['obj_type_id']) ) ? (int) trim($_POST['obj_type_id']) : null;
		$obj_type_det = (check_index($obj_type_id) ) ? get_type($obj_type_id) : null;

		// See if any of the select buttons were pressed
		if (isset($_POST['edit_obj']) )
		{
			foreach($_POST['edit_obj'] as $key_obj_id=>$edit_obj_tf)
			{
				if ($edit_obj_tf == 'true')
				{
					$sel_obj_id = $key_obj_id;
					}	# End of tf conditional
				}	# End of foreach
			}	# End of isset

		$edit_obj_id = (isset($sel_obj_id) && check_index($sel_obj_id) ) ? $sel_obj_id : null;
		$sel_obj_name = "";
		$sel_obj_dsr = "";
		$sel_acs_str = "";

		if (isset($_POST['repage']) && isset($_POST['addnew']) )
		{
			$new_obj = create_pg_obj($obj_type_id, $_POST['obj_name'], $_POST['obj_dsr'], $_POST['acs_str']);

			// Deal with adding new objects to existing pages
			if (isset($_POST['assign']) && $_POST['assign'] == 'true')
			{
				$pg_list = get_pg_list($_POST['pg_ctx_id']);

				foreach ($pg_list as $pg_list_key=>$pg)
				{
					update_pg_brg($pg['pg_id'], $new_obj, $_POST['location']);

					// Deal with adding the object to the end of the object list for each page
					if (isset($_POST['order']) && $_POST['order'] == 'true')
					{
						append_obj_spc_ord($pg['pg_id'], $new_obj);
						}
					}
				}
			}
		elseif (isset($_POST['repage']) && isset($_POST['updateobj']) )
		{
			$update_obj = update_pg_obj($_POST['sel_update_obj_id'], $_POST['obj_name'], $_POST['obj_dsr'], $_POST['acs_str']);
			unset($_POST['updateobj'], $_POST['sel_update_obj_id'], $_POST['obj_name'], $_POST['obj_dsr'], $_POST['acs_str']);
			}
		elseif (isset($_POST['repage']) && isset($_POST['activebit']) )
		{
			foreach($_POST['obj_hid'] as $pg_obj_id)
			{
				if (isset($_POST['obj_act'][$pg_obj_id]) && $_POST['obj_act'][$pg_obj_id] = $pg_obj_id)
					{$tf = true;} else {$tf = false;}
				$pg_tf = set_pg_obj_tf($pg_obj_id, $tf);
				}
			} 	# end of repage conditional
		
		// We need the latest values
		if (isset($_POST['repage']) && isset($sel_obj_id) )
		{
			extract(sel_pg_obj($sel_obj_id) );
			} # end of repage conditional

		// ________________________________________________________________________________
		$obj_list = get_pg_obj_list($obj_type_id);
		$sel_obj_type_name = (isset($obj_type_det['type_name']) ) ? $aoo('span', "core={$obj_type_det['type_name']} ({$obj_type_id}) [{$obj_type_det['std_type_lbl']}]") : null;
		$sel_obj_type_dsr = $aoo('span', "core=&mdash; {$ps($obj_type_det['type_dsr'])}");

		echo "<fieldset><legend>All Objects of Type $sel_obj_type_name, if any</legend>";

$tabletop = <<<TABLETOP
<table>
<caption>Objects cannot be updated here</caption>
<thead>
<tr>
    <th style="text-align:center;" >Object ID</th>
    <th>Object Name</th>
    <th>Description</th>
    <th>Access String</th>
    <th style="text-align:center;" >Active</th>
    <th style="text-align:center;" >Edit</th>
</tr>
</thead>
<tbody>
TABLETOP;

		echo $tabletop;

		$i = 1;

		foreach($obj_list as $obj_key=>$obj)
		{
			extract($obj);
			$bc = row_color($i, null, "#DDD");
			$obj_hid = $aoo('hidden', "name=obj_hid[];\nvalue=$obj_id");
			if (isset($_POST['edit_obj'][$obj_id]) && $_POST['edit_obj'][$obj_id]=='true') {$button_class = "class=button_active";} else {$button_class = "class=button_passive";}

$tabledata = <<<TABLEDATA
<tr>\n
    <td style="$bc text-align:center; ">$obj_id</td>\n
    <td style="$bc ">$obj_name</td>\n
    <td style="$bc ">$obj_dsr</td>\n
    <td style="$bc ">$acs_str</td>\n
    <td style="$bc text-align:center; ">{$aoo('cb', "name=obj_act[$obj_id];\nvalue=$obj_id;\ntf=$act_bit")}{$obj_hid}</td>\n
    <td style="$bc text-align:center; ">{$aoo('button', "{$button_class};\nname=edit_obj[$obj_id];\nvalue=true;\nlabel=Select")}</td>\n
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
    <td colspan="6" style="$bc text-align:center; ">Please Create Your First {$obj_type_det['type_name']} Page Object Below!</td>\n
 </tr>\n
TABLEDATA;

			echo $tabledata;

			$buttontype = "null";
			}
		else {$buttontype = "button";}
		
		echo "</tbody>\n";
		echo "</table>\n";

		echo	$aoo($buttontype, "class=button_passive;\nname=activebit;\nvalue=1;\nlabel=Update Active Object List;\nfore=<br>;\naft=<br></fieldset><br>");

		// ________________________________________________________________________________
		$pg_ctxs = get_ctx_by_type(36, true);

		echo "<fieldset><legend>Add/Edit Page Object of Type $sel_obj_type_name $sel_obj_type_dsr</legend>\n";

		echo $aoo('textbox', "name=obj_name;\nsize=50;\nmax=50;\nvalue=$sel_obj_name;\naft=Object Name (Unique for each Type and Required)<br>");

		echo $aoo('textbox', "name=acs_str;\nsize=50;\nmax=50;\nvalue=$sel_acs_str;\naft=Access String (Not Required)<br>");

		echo $aoo('textarea', "name=obj_dsr;\nrows=5;\nvalue=$sel_obj_dsr;\naft=<br>Object Description (Highly Desirable)<br>");

		$pgctxid = (isset($_POST['pg_ctx_id']) && check_index($_POST['pg_ctx_id']) ) ? $_POST['pg_ctx_id'] : '';
		
		$pg_ctx_sel = $aoo('select', "class=sel_css;\nname=pg_ctx_id;\ncore={$aoo('option', "value={$pg_ctxs['ctx_id']};\ntval={$pgctxid};\ndval=2;\nlabel={$pg_ctxs['ctx_opt_lbl']}")}");

		//"
		echo $aoo('cb', "name=assign;\nvalue=true;\nlabel=&nbsp;Assign new object to existing pages of context $pg_ctx_sel at location:&nbsp;;\nfore=<br>");
		echo $aoo('textbox', "name=location;\nsize=30;\nmax=255;\naft=<br>");
		echo $aoo('cb', "name=order;\nvalue=true;\nlabel=&nbsp;Place object at the end of the object list for each page.;\naft=<br>");

		echo $aoo('button', "class=button_passive;\nname=addnew;\nvalue=1;\nlabel=Add Object;\nfore=<br>");

		if (check_index($edit_obj_id) ) {echo $aoo('button', "class=button_passive;\nname=updateobj;\nvalue=1;\nlabel=Update Object");}

		echo $aoo('reset', "class=button_passive");

		$sel_name_span = $aoo('span', "core=$sel_obj_name ($edit_obj_id)");
		if (check_index($edit_obj_id) ) {echo "<br><br>Currently editing object $sel_name_span. (You can still create a new object with Add Object.  Just give it a new name.)";}

		echo "</fieldset><br>\n";

		echo $aoo('hidden', "name=repage;\nvalue=true");
		echo $aoo('hidden', "name=obj_type_id;\nvalue=$obj_type_id");
		echo $aoo('hidden', "name=sel_update_obj_id;\nvalue=$edit_obj_id");

		//--2-- End of 2nd form
		echo "</form>";
		} # End of original post conditional

?>
