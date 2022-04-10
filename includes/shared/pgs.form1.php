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
	
	$source_pg = "N&sol;A";

	// Get all the current pages
	$pg_id_list = create_pg_dropdown();

	$pg_ids = explode('__r__', $pg_id_list['pg_id']);
	$pg_names = explode('__r__', $pg_id_list['pg_name']);

	if (isset($_POST['pg_id']) && is_numeric($_POST['pg_id']) ) 
	{
		$pg_id_key = array_search($_POST['pg_id'], $pg_ids);
		$source_pg = $pg_names[$pg_id_key];
		}

	echo "<fieldset><legend>Existing Pages [page context] (ID)</legend>";

	// Display all the pages.
	if (isset($pg_id_list['pg_id']) && !empty($pg_id_list['pg_id']) )
	{
		$post_pg_id = (isset($_POST['pg_id']) && check_index($_POST['pg_id']) ) ? $_POST['pg_id'] : '';
		echo $aoo('select', "class=sel_css;\nname=pg_id;\ncore={$aoo('option', "value={$pg_id_list['pg_id']};\ntval={$post_pg_id};\nlabel={$pg_id_list['pg_dsr']};\nlabelaft=&nbsp;")};\nonchange=clickB()");

		echo $aoo('button', "class=button_passive;\nname=selpage;\nvalue=true;\nlabel=Select Page;\nid=seltype");
		
		// If the page is reloading page=true, and we want the existing value of obj_filter to have precedence
		// Otherwise, we want the default value dtf to take over
		if ( (isset($_POST['page']) && $_POST['page'] == 'true') || (isset($_POST['repage']) && $_POST['repage'] == 'true') )
			{$post_obj_filter = (isset($_POST['obj_filter']) && $_POST['obj_filter'] == 1) ? $_POST['obj_filter'] : 'off';}
		else
			{$post_obj_filter = '';}
		
		if (isset($show_obj_filter) && $show_obj_filter) {echo $aoo('cb', "name=obj_filter;\nclass=bump_right;\nvalue=1;\ndtf=1;\ntf=$post_obj_filter;\nlabel=&nbsp;Ignore Unassigned Objects");}
		if ($post_obj_filter == 'off') {$post_obj_filter = false;}
		}
	else
	{
		echo $aoo('div', "class=divback;\ncore=<br>You Need to Create at Least One Page First! {$aoo('link', "style=font-weight: bold; font-style:italic;;\nhref=hoopla.fw.create.pgs.php;\ncore=Create Pages")}<br>&nbsp;");
		}

	echo "</fieldset><br>";

	echo $aoo('hidden', "name=page;\nvalue=true");
	//--1-- End of the first form
	echo "</form>";

?>
