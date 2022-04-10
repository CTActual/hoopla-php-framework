<?php
/*
Copyright 2009-2021 Cargotrader, Inc. All rights reserved.

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

			// Shared code for new object values or editing existing values

			echo "<fieldset><legend>Enter Object $sel_obj_name_span Values for the Page $sel_pg_name or Pageless Default Values</legend>\n";

			$act_ctxes = get_act_ctx();
			$i = 1;
			$tabletop = "";
			$tabledata = "";

			foreach ($act_ctxes as $ctx_key=>$ctx)
			{
				extract($ctx);
				$ctx_hid = $aoo('hidden', "name=ctx_hid[$i];\nvalue=$ctx_id");
				$sel_ctx_name_span = $aoo('span', "core=$ctx_name ($ctx_lbl)");

$tabletop = <<<TABLETOP
<summary>$sel_ctx_name_span</summary>
<table>
<caption><b>Values for the $sel_ctx_name_span context</b>$ctx_hid</caption>
<thead>
<tr>
    <th style="width:15%; text-align:center;" >Page Association</th>
    <th style="width:5%; text-align:center;" >Active<br>Value</th>
    <th>Value</th>
</tr>
</thead>
<tbody>
TABLETOP;

				$tabledata = "";
				$val_size = 0;
				
				for ($j=1;$j<3;$j++)
				{
					// $j == 1 for the pg specific value, and $j == 2 for the default value
					
					$bc = row_color($j, null, "#DDD");

					if ($j == 1)
					{
						$com_pg_id = $pg_id;

						$type_lbl = 'pg_';

						$box1 = "For $sel_pg_name";
						}
					else
					{
						$com_pg_id = null;

						$type_lbl = 'def_';

						$box1 = ($use_def_bit) ? "Default for Any Page" : $aoo('span', "core=Any Page Default Unused on this Page");
						}

					unset($val_id);
					unset($val);
					unset($val_act_bit);
					$val_id = null;
					$val = null;
					extract(get_val($sel_obj_id, $_POST['obj_sets'], $com_pg_id, $ctx_id) );

					if (isset($val_id) && check_index($val_id) ) 
					{
						$act_obj = $aoo('cb', "name=val_{$type_lbl}act_obj[$i];\nvalue=$val_id;\ntf=$val_act_bit;\ndval=1");

						if ($val_act_bit)
							{$act_bit_hid = $aoo('hidden', "name={$type_lbl}act_bit_hid[$i];\nvalue=true");}
						else
							{$act_bit_hid = $aoo('hidden', "name={$type_lbl}act_bit_hid[$i];\nvalue=false");}

						$val_id_hid = $aoo('hidden', "name={$type_lbl}val_id_hid[$i];\nvalue=$val_id");
						} 
					else 
					{
						$act_obj = "Create<br>New<br>Value";
						$act_bit_hid = $aoo('hidden', "name={$type_lbl}act_bit_hid[$i];\nvalue=true");
						$val_id_hid = null;
						}

					$val_size = $val_size + strlen($val);
					$fixed_val = htmlspecialchars($val, ENT_QUOTES | ENT_HTML5, "UTF-8");
					$val_box = $aoo('textarea', "name={$type_lbl}val[$i];\nrows=5;\nvalue=$fixed_val");

$tabledata .= <<<TABLEDATA
<tr>\n
    <td style="width:15%; text-align:center;$bc ">$box1</td>\n
    <td style="width:5%; text-align:center;$bc ">$act_obj&nbsp;{$act_bit_hid}</td>\n
    <td style="$bc ">$val_box$val_id_hid</td>\n
</tr>\n
TABLEDATA;
					}	# End of for loop

				$total_table = "$tabletop{$tabledata}</tbody></table>";
				
				if ($val_size > 0 || count($act_ctxes) == 1) {$open = "open=true;\n";} else {$open = "";}
				
				echo $aoo('details', "{$open}core=$total_table");
				echo "<br>\n";
				$i++;
				}	# End of context loop

			echo $aoo('button', "class=button_passive;\nname=objvals;\nvalue=true;\nlabel=Save or Update Values");
			echo "</fieldset><br>";


?>
