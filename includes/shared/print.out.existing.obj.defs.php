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

			// List the names of the default values, if any
			$def_str = "Active default values exist for context(s) <i>[Click on a Select button to modify]</i>: <br>";
			$defcore = '';
			
			foreach ($def_vals as $def_val_key=>$def_ctx)
			{
				extract($def_ctx);

				$trail = (strlen($def_val) > 511) ? ' ...' : '';
				
				$def_sub = $aoo('span', "class=mild_span;\ncore={$ps($def_val, 511)}$trail");
				
				$defcore .= "$def_ctx_name ($def_ctx_lbl) as $def_type_name ($def_type_std_lbl), $def_sub<br>";
				}

			if (count($def_vals) > 0)
			{
				$defcore = substr($defcore, 0, -2) . '.';
				$def_str .= $aoo('span', "class=bold_span;\ncore=$defcore");
				
$tabledata = <<<TABLEDATA
<tr>\n
    <td style="$bc" colspan="$colspan">$def_str</td>\n
 </tr>\n
TABLEDATA;

				echo $tabledata;
				}
?>
