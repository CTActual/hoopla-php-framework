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

			// Get the page object setting type values
			$set_type_list = list_pg_obj_set_types($sel_pg_obj_type_id);

			if (count(explode('__r__', $set_type_list['pg_obj_set_type_id']) ) == 1) {$def_set_type_id = $set_type_list['pg_obj_set_type_id'];} else {$def_set_type_id = null;}

			$post_obj_sets = (isset($_POST) && isset($_POST['obj_sets']) && check_index($_POST['obj_sets']) ) ? $_POST['obj_sets'] : $def_set_type_id;

			# If there is only one setting type in use we default to that, unless the user selects a new one.
			$set_type_count = det_num_of_set_types_per_obj($sel_obj_id, $pg_id);

			$use_def_bit = det_use_def_bit($sel_obj_id, $pg_id);

			// Get the existing object values again
			$pg_obj_vals = get_vals_by_pg_and_obj($pg_id, $sel_obj_id);
			$pg_obj_set_type_ids = get_pg_val_set_type_ids($pg_id, $sel_obj_id);
			$mlist = (isset($pg_obj_set_type_ids) && isset($pg_obj_set_type_ids['val_pg_obj_set_type_id']) && is_array($pg_obj_set_type_ids['val_pg_obj_set_type_id']) ) ? flatten($pg_obj_set_type_ids['val_pg_obj_set_type_id']) : null;

			// If there is more than zero setting types and existing values, we can pick the first existing value.
			if (!isset($post_obj_sets) && $set_type_count > 0)
			{
				if (isset($pg_obj_vals) && isset($pg_obj_vals[0]) && isset($pg_obj_vals[0]['val_pg_obj_set_type_id']) && check_index($pg_obj_vals[0]['val_pg_obj_set_type_id']) ) 
					{$post_obj_sets = $pg_obj_vals[0]['val_pg_obj_set_type_id'];}
				elseif (isset($def_vals) && isset($def_vals[0]) && isset($def_vals[0]['def_type_id']) && check_index($def_vals[0]['def_type_id']) )
					{$post_obj_sets = $def_vals[0]['def_type_id'];}
				}

			echo $aoo('form');	#--3--

			// fieldset for the setting type box
			echo $aoo('fieldset', "id=openbox;\nlegend=Select the Setting Type for the Object: $sel_obj_name_span");

			echo $aoo('select', "class=sel_css;\nname=obj_sets;\ncore={$aoo('option', "value={$set_type_list['pg_obj_set_type_id']};\ntval={$post_obj_sets};\ncore={$set_type_list['set_type_list']};\ncoreaft=&nbsp;;\nmarklist=$mlist")};\nonchange=clickAny('settingtype')");

			// "Select setting type button
			echo $aoo('button', "class=button_passive;\nname=settype;\nvalue=true;\nlabel=Select Type;\nid=settingtype");
			echo "</fieldset>\n<br>\n";



?>
