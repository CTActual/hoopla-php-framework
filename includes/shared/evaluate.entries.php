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

// Evaluate any entries that change page object values, default values in any context

		$val_id = (isset($_POST[$type_lbl . 'val_id_hid'][$k]) && check_index($_POST[$type_lbl . 'val_id_hid'][$k]) ) ? $_POST[$type_lbl . 'val_id_hid'][$k] : null;

		$val_act_obj = (isset($_POST['val_' . $type_lbl . 'act_obj'][$k]) && check_index($_POST['val_' . $type_lbl . 'act_obj'][$k]) ) ? $_POST['val_' . $type_lbl . 'act_obj'][$k] : null;

		evaluate_entries($upd_obj_id, $_POST['obj_sets'], $upd_pg_id, $_POST['ctx_hid'][$k], $val_id, $_POST[$type_lbl . 'act_bit_hid'][$k], $val_act_obj, $_POST[$type_lbl . 'val'][$k]);

?>
