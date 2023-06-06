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
			// Print out the existing object values
			if (is_array($pg_obj_vals) )
			{
				foreach ($pg_obj_vals as $pg_obj_val_key=>$pg_obj_val)
				{
					if (!empty($pg_obj_val['val']) || $pg_obj_val['val'] === 0 || $pg_obj_val['val'] === '0')
					{
						$active_val = ($pg_obj_val['val_act_bit']) ? 'Active ' : 'Inactive ';
						
						$st = $aoo('span', "class=med_span;\ncore={$pg_obj_val['val_set_type_name']}");
						$stlbl = $aoo('span', "class=mild_span;\ncore=({$pg_obj_val['val_set_type_lbl']})");
						
						$trail = (strlen($pg_obj_val['val']) > 511) ? ' ...' : '';
						
						$obj_val_sub = $aoo('span', "class=mild_span;\ncore={$ps($pg_obj_val['val'], 511)}$trail");
						
						$val_ctx_name = $aoo('span', "class=big_span;\ncore={$pg_obj_val['val_ctx_name']}");
						
						$val_ctx_lbl = $aoo('span', "class=lbl_border big_span;\ncore={$pg_obj_val['val_ctx_lbl']}");
						
						if (isset($_POST['sel_obj_val']) && is_array($_POST['sel_obj_val']) && isset($_POST['sel_obj_val'][$pg_obj_val['val_id']]) && $_POST['sel_obj_val'][$pg_obj_val['val_id']] == 'true') 
						{
							$fixed_val = htmlspecialchars($pg_obj_val['val'], ENT_QUOTES | ENT_HTML5, "UTF-8");
							$val_box = "<br><br>" . $aoo('textarea', "name=change_val[{$pg_obj_val['val_id']}];\nrows=5;\nvalue=$fixed_val");
							$val_box .= $aoo('hidden', "name=change_existing_val;\nvalue={$pg_obj_val['val_id']}");
							$val_box .= $aoo('hidden', "name=existing_val_state;\nvalue=$active_val");
							
							$class = "class=button_active;\n";
							$butlabel = "Save";
							$div_id = "id=openbox;\n";
							} 
						else 
						{
							$val_box = "";
							$class="class=button_passive;\n";
							$butlabel = "Edit";
							$div_id = "";
							}
							
						$toprow = $aoo('div', "{$div_id}style=margin-bottom:3px;;\ncore={$active_val}Value Entered For");
						$strow = $aoo('div', "style=margin-top:3px; margin-bottom:10px;;\ncore=Setting Type: {$st}&nbsp;{$stlbl}");
						$val_dsr = "{$toprow}Context Type: {$pg_obj_val['val_ctx_type_name']} - {$val_ctx_name}$val_ctx_lbl<br>$strow{$obj_val_sub}$val_box";

						$editbut = $aoo('button', "{$class}name=sel_obj_val[{$pg_obj_val['val_id']}];\nvalue=true;\nlabel=$butlabel");

$tabledata = <<<TABLEDATA
<tr>\n
    <td style="$bc" colspan="$colspan">$val_dsr</td>\n
    <td style="text-align:center; $bc">$editbut</td>\n
</tr>\n
TABLEDATA;

						echo $tabledata;
						}	# End of !empty
					}	# End of foreach
				}	# End of is_array
?>
