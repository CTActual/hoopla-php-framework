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

	$pg_obj_type_list = pg_obj_type_list(true, false);

	echo "<fieldset><legend>Page Object Types [shorthand] (ID)</legend>";

	// Display all the object types.
	$post_obj_types = (isset($_POST['obj_types']) && !empty($_POST['obj_types']) ) ? $_POST['obj_types'] : null;
	echo $aoo('select', "class=sel_css;\nname=obj_types;\ncore={$aoo('option', "value={$pg_obj_type_list['type_id']};\ntval={$post_obj_types};\ncore={$pg_obj_type_list['type_lbl']};\ncoreaft=&nbsp;")};\nonchange=clickB()");

	echo $aoo('button', "class=button_passive;\nname=seltype;\nvalue=1;\nlabel=Select Object Type;\nid=seltype");

	echo "<br></fieldset><br>\n";

	echo $aoo('hidden', "name=page;\nvalue=true");
	//--1-- End of the first form
	echo "</form>";

//__________________________________________________________________________________________________________
	if (isset($_POST['page']) || isset($_POST['repage']) )
	{
		if (isset($_POST['repage']) && check_index($_POST['obj_types']) && isset($_POST['set_type_hid1']) && isset($_POST['set_type_hid2']) )
		{
			// Update all the brg values or add as necessary
			foreach ($_POST['set_type_hid1'] as $post_key=>$set_type_id)
			{
				// We only worry about the changes that need to be made

				if (isset($_POST['set_type'][$post_key]) && $_POST['set_type'][$post_key] == $set_type_id)
				{
					// If the value is checked off to be true and was false, we set to true
					if ($_POST['set_type_hid2'][$post_key] == 'false')
						{$update = update_pg_obj_type_pg_obj_set_type_brg($_POST['obj_types'], $set_type_id);}
					}
				elseif ($_POST['set_type_hid2'][$post_key] == 'true')
				{
					// We set the value to false
					$update = update_pg_obj_type_pg_obj_set_type_brg($_POST['obj_types'], $set_type_id, false);
					}
				}
			} # end of repage conditional
//__________________________________________________________________________________________________________

		// Get all the object setting types
		$obj_set_types = get_types(2);

		// Get all the current bridge records
		if (isset($_POST['obj_types']) && check_index($_POST['obj_types']) )
		{
			$act_set_types = get_pg_obj_set_types($_POST['obj_types']);
			$act_type = get_type($_POST['obj_types']);
			}
		else
		{
			$act_set_types = array();
			}

		echo $goo('form');	#--2--

		// Get the selected object type
		$sel_type_span = $aoo('span', "core={$act_type['type_name']} ({$_POST['obj_types']}) [{$act_type['std_type_lbl']}] &mdash; {$ps($act_type['type_dsr'])}");
		echo "<fieldset><legend>Setting Types for Object Type: $sel_type_span</legend>";

		$pg_set_type = (isset($act_set_types['pg_obj_set_type_id']) && is_array($act_set_types['pg_obj_set_type_id']) && count($act_set_types['pg_obj_set_type_id']) > 0) ? $act_set_types['pg_obj_set_type_id'] : array(0);

		$i = 1;

		foreach ($obj_set_types as $obj_set_type_key=>$obj_set_type)
		{
			extract($obj_set_type, EXTR_PREFIX_ALL, 'set');

			$tf_bit = false;
			$tf = 'false';

			// Check the bridge array to see if the current setting applies -- $set_type_id comes from $mysqli #2
			if (in_array($set_type_id, $pg_set_type) )
			{
				$tf_bit = true;
				$tf = 'true';
				}

			echo $aoo('hidden', "name=set_type_hid1[$i];\nvalue=$set_type_id");
			echo $aoo('hidden', "name=set_type_hid2[$i];\nvalue=$tf");
			echo $aoo('cb', "name=set_type[$i];\nvalue=$set_type_id;\ntf=$tf_bit;\nlabel=&nbsp;$set_type_name ($set_type_dsr);\naft=<br>");
			$i++;
	       	}	# End of foreach
		
		echo $aoo('button', "class=button_passive;\nname=savesettings;\nvalue=true;\nlabel=Save Setting Types;\nfore=<br>\n");
		echo $aoo('reset', "class=button_passive");
		echo "<br>\n</fieldset><br>\n";

		echo $aoo('hidden', "name=repage;\nvalue=true");
		echo $aoo('hidden', "name=obj_types;\nvalue={$_POST['obj_types']}");

		//--2-- End of the second form
		echo "</form>";
		} # End of original post conditional

?>
