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

		if (isset($_POST['repage']) && isset($_POST['save']) )
		{
			// Set all the active records to false for the page to make things easy
			$update_result = set_pg_pg_obj_brg_act_bit_false($pg_id);
			$use_def_bit_result = set_pg_pg_obj_brg_use_def_bit_false($pg_id);
			$use_def_ctx_bit_result = set_pg_pg_obj_brg_use_def_ctx_bit_false($pg_id);

			// Now we see if anything came of the entry process
			// Go thru each object to see if they were selected (only active objects get an update)
			if (isset($_POST['act_obj']) ) 
			{
				while (list($val_id, $id_val) = each($_POST['act_obj']) )
				{
					$insert_result = set_pg_pg_obj_brg_act_bit_true($pg_id, $id_val);
					}	# End of while
				}	# End of act_bit updates

			if (isset($_POST['use_def_obj']) ) 
			{
				foreach($_POST['use_def_obj'] as $use_def_key=>$id_val)
				{
					$insert_use_def_result = set_pg_pg_obj_brg_use_def_bit($pg_id, $id_val);
					}	# End of foreach
				}	# End of use_def_bit updates

			if (isset($_POST['use_def_ctx_obj']) ) 
			{
				foreach($_POST['use_def_ctx_obj'] as $use_def_key=>$id_val)
				{
					$insert_use_def_ctx_result = set_pg_pg_obj_brg_use_def_ctx_bit($pg_id, $id_val);
					}	# End of foreach
				}	# End of use_def_ctx_obj updates

			$non_pg_obj_list = get_non_pg_obj_ids();

			foreach($non_pg_obj_list['non_pg_obj_ids'] as $non_pg_key=>$all_pg_obj_id)
			{
				$obj_loc = (isset($_POST['obj_loc_' . $all_pg_obj_id]) && !empty($_POST['obj_loc_' . $all_pg_obj_id]) ) ? trim($_POST['obj_loc_' . $all_pg_obj_id]) : null;
				$spc_ord = (isset($_POST['obj_spc_ord_' . $all_pg_obj_id]) && !empty($_POST['obj_spc_ord_' . $all_pg_obj_id]) ) ? trim($_POST['obj_spc_ord_' . $all_pg_obj_id]) : null;
				$updated_loc = update_obj_loc($pg_id, $all_pg_obj_id, $obj_loc);
				$updated_spc_ord = update_obj_spc_ord($pg_id, $all_pg_obj_id, $spc_ord);
				}	# End of foreach
			} # end of repage conditional
		else
		{
			include($sharedpath . 'upd.set.val.php');
			}

?>
