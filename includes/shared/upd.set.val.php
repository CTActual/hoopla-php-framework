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

	// Shared code for updating individual setting values
	
		if (isset($_POST['repage']) && $_POST['repage'] == 'true' && isset($_POST['change_existing_val']) && check_index($_POST['change_existing_val']) && isset($_POST['change_val']) && isset($_POST['sel_obj_val']) && is_array($_POST['sel_obj_val']) && isset($_POST['sel_obj_val'][$_POST['change_existing_val'] ]) && $_POST['sel_obj_val'][$_POST['change_existing_val'] ] == 'true')
		{
			$change_of_state = (isset($_POST['existing_val_state']) && $_POST['existing_val_state'] == 'Active ') ? true : false;
			
			// Local edit where the value still exists
			if (isset($_POST['change_val'][$_POST['change_existing_val'] ]) )
			{
				update_val($_POST['change_existing_val'], $change_of_state, $_POST['change_val'][$_POST['change_existing_val'] ]);
				}
			else
			{
				update_val($_POST['change_existing_val'], $change_of_state, null);
				}
			}
	
?>