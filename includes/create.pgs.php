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

	echo $aoo('form');	#--1--

	$sel_obj_id = 0;
	$sel_obj_name = "";
	$sel_obj_dsr = "";
	$sel_obj_loc = "";
	$sel_acs_str = "";
	$sel_pg_ctx_id = 0;
	$sel_pg_ctx_name = "";
	$sel_pg_ctx_lbl = "";

	if (isset($_POST['page']) && isset($_POST['newpage']) )
	{
		$pg_brg_id = create_pg($_POST['obj_name'], $_POST['obj_dsr'], $_POST['acs_str'], $_POST['url_tag'], $_POST['pg_ctx_id']);

		// Deal with cloning of objects
		if (isset($_POST['clone_pg_id']) && check_index($_POST['clone_pg_id']) && $_POST['clone_pg_id'] != '0' && check_index($pg_brg_id) )
		{
			clone_pg_objs($_POST['clone_pg_id'], get_pg_id_from_pg_pg_obj_brg_id($pg_brg_id) );
			}
		}
	elseif (isset($_POST['page']) && isset($_POST['activebit']) && isset($_POST['pg_hid']) )
	{
		foreach($_POST['pg_hid'] as $pg_obj_id)
		{
			if (isset($_POST['pgs_act'][$pg_obj_id]) && $_POST['pgs_act'][$pg_obj_id] = $pg_obj_id)
				{$tf = true;} else {$tf = false;}
			$pg_tf = set_pg_obj_tf($pg_obj_id, $tf);
			}
		} 
	elseif (isset($_POST['page']) && isset($_POST['updatepage']) )
	{
		$upd_pg = update_pg_obj($_POST['sel_pg_obj_id'], $_POST['obj_name'], $_POST['obj_dsr'], $_POST['acs_str']);
		$upd_pg_brg = update_pg_brg($_POST['sel_pg_id'], $_POST['sel_pg_obj_id'], $_POST['url_tag']);
		$upd_pg_ctx = update_pg_ctx_id($_POST['sel_pg_id'], $_POST['pg_ctx_id']);
		} 
	elseif (isset($_POST['page']) && isset($_POST['edit_pg']) )
	{
		foreach($_POST['edit_pg'] as $edit_pg_id=>$edit_pg_tf)
		{
			unset($_POST['obj_name'], $_POST['obj_dsr'], $_POST['acs_str'], $_POST['url_tag'], $_POST['pg_ctx_id']);
			
			if ($edit_pg_tf == 'true')
				{extract(get_pg_info($edit_pg_id) );}	
			}	# end of foreach
		}	# end of page conditional

// ______________________________________________________________________________________________________________
	// Clean up empty string records
	$null_output = clean_up_pg_obj_nulls();
// ______________________________________________________________________________________________________________
	$pg_list = get_pg_list();

	echo "<fieldset><legend>Page Listing (Page IDs or Tags and not their object IDs determine how a page is located by URL)</legend>";

$tabletop = <<<TABLETOP
<table>
<caption>Add/Edit Pages</caption>
<thead>
<tr>
    <th>Page ID</th>
    <th>Page Name</th>
    <th>Description</th>
   <th>URL Tag</th>
    <th>Page<br>Order</th>
  <th>Access<br>String</th>
    <th style="text-align:center;" >Page<br>Obj ID</th>
    <th style="text-align:center;" >Page Context</th>
    <th style="text-align:center;" >Active</th>
    <th style="text-align:center;" >Edit</th>
</tr>
</thead>
<tbody>
TABLETOP;

			echo $tabletop;

			$i = 1;

	foreach ($pg_list as $pg_list_key=>$pg)
	{
		extract($pg);

		$bc = row_color($i, null, "#DDD");

		$pgs_bit = $aoo('cb', "name=pgs_act[$obj_id];\ntf=$act_bit;\nvalue=$obj_id");
		$pg_hid = $aoo('hidden', "name=pg_hid[];\nvalue=$obj_id");

		if (isset($edit_pg_id) && $pg_id == $edit_pg_id) {$button_class = "class=button_active";} else {$button_class = "class=button_passive";}
		$edit_button = $aoo('button', "{$button_class};\nname=edit_pg[{$pg_id}];\nvalue=true;\nlabel=Select");

$tabledata = <<<TABLEDATA
<tr>\n
    <td style="$bc text-align:center; ">$pg_id</td>\n
    <td style="$bc ">$obj_name</td>\n
    <td style="$bc ">$obj_dsr</td>\n
    <td style="$bc ">$url_tag</td>\n
     <td style="$bc text-align:center; ">$pg_ord</td>\n
   <td style="$bc ">$acs_str</td>\n
    <td style="$bc text-align:center; ">$obj_id</td>\n
    <td style="$bc text-align:center; ">$pg_ctx_name ($pg_ctx_id)</td>\n
    <td style="$bc text-align:center; ">$pgs_bit{$pg_hid}</td>\n
    <td style="$bc text-align:center; ">$edit_button</td>\n
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
    <td colspan="10" style="$bc text-align:center; ">Please Create Your First Page Below!</td>\n
</tr>\n
TABLEDATA;

		echo $tabledata;
		}

	echo "</tbody>";
	echo "</table><br>\n";
	echo $aoo('button', "class=button_passive;\nname=activebit;\nvalue=true;\nlabel=Update Active Page List;\naft=<br>");
	echo "<br></fieldset><br>\n";

//_____________________________________________________________________________________________________________
	# Box for editing page information

	$pg_ctxs = get_ctx_by_type(36, true);
	$sel_pg_id = (isset($edit_pg_id) ) ? $edit_pg_id : null;

	// Get all the current pages for a cloning pg objects dropdown
	$pg_id_list = create_pg_dropdown();
	
	if (!empty($pg_id_list['pg_id']) && !empty($pg_id_list['pg_dsr']) )
	{
		$pg_id_list['pg_id'] = '0__r__' . $pg_id_list['pg_id'];
		$pg_id_list['pg_dsr'] = 'Do Not Clone Any Objects__r__' . $pg_id_list['pg_dsr'];
		$clone = $aoo('select', "class=sel_css sel_css_u;\nname=clone_pg_id;\ncore={$aoo('option', "value={$pg_id_list['pg_id']};\ntval={$sel_pg_id};\nlabel={$pg_id_list['pg_dsr']};\nlabelaft=&nbsp;")};\naft=&nbspClone Objects From Existing for New Page<br>");
		}
	else {$clone = '';}
	
	// See if this is a new page or selected page
	// Note we show the pg object id and not the page id of the selected page
	if (isset($sel_obj_id) && check_index($sel_obj_id) ) 
		{$legend = "Create New Page or Edit Page {$aoo('span', "core=$sel_obj_name ($sel_obj_id)")}";} 
	else 
		{$legend = "Create Page";}

	echo "<fieldset><legend>$legend</legend>";
	echo $aoo('textbox', "name=obj_name;\nsize=50;\nmax=50;\nvalue=$sel_obj_name;\nlabel=&nbsp;Page Name (Unique and Required);\naft=<br>");
	echo $aoo('textbox', "name=url_tag;\nsize=50;\nmax=50;\nvalue=$sel_obj_loc;\nlabel=&nbsp;Optional URL Tag (Good Idea but Not Required);\naft=<br>");
	echo $aoo('textbox', "name=acs_str;\nsize=50;\nmax=50;\nvalue=$sel_acs_str;\nlabel=&nbsp;Added Security Access String (Not Required);\naft=<br>");
	
	$pg_ctx_id = (isset($_POST['pg_ctx_id']) && check_index($_POST['pg_ctx_id']) ) ? $_POST['pg_ctx_id'] : $sel_pg_ctx_id;
	echo $aoo('select', "class=sel_css sel_css_m;\nname=pg_ctx_id;\ncore={$aoo('option', "value={$pg_ctxs['ctx_id']};\ntval={$pg_ctx_id};\ndval=2;\nlabel={$pg_ctxs['ctx_opt_lbl']}")};\nlabel=&nbsp;Page Context;\naft=<br>");
	
	echo $aoo('textarea', "name=obj_dsr;\nrows=5;\ncols=70;\nvalue=$sel_obj_dsr;\nlabel=&nbsp;<br>Page Description (Highly Desirable);\naft=<br>");

	echo $clone;
	
	echo $aoo('button', "fore=<br>;\nclass=button_passive;\nname=newpage;\nvalue=true;\nlabel=Add Page");
	if (check_index($sel_obj_id) ) {echo $aoo('button', "class=button_passive;\nname=updatepage;\nvalue=true;\nlabel=Update Page");}

	echo $aoo('reset', "class=button_passive");

	$sel_name_span = $aoo('span', "core=$sel_obj_name ($sel_obj_id)");
	if (check_index($sel_obj_id) ) {echo "<br><br>Currently editing page $sel_name_span. (You can still create a new page by using a new name.)";}
	
	echo "<br></fieldset><br>\n";

	echo $aoo('hidden', "name=page;\nvalue=true");
	echo $aoo('hidden', "name=sel_pg_obj_id;\nvalue=$sel_obj_id");
	echo $aoo('hidden', "name=sel_pg_id;\nvalue=$sel_pg_id");

	//--1-- End of Form 1
	echo "</form>";

?>
