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

// Classes and functions for creating and modifying arbitrary contexts

//____________________________________________________________________________________
function get_ctx_types($flatten=true)
{
	$flatten = force_boolean($flatten, true);

	# Get the context types to list
	$types_sql = "Select types.id, 
		types.spc_ord, 
		types.type_name, 
		types.type_dsr, 
		CONCAT_WS('', type_name, ' (', id, ') ', '[', type_dsr, '] ') as opt_lbl 
	From types 
	Where types.meta_type_id = 3 
	Order by types.spc_ord, 
		types.type_name";

	return col_pattern($types_sql, null, null, array('type_id', 'spc_ord', 'type_name', 'type_dsr', 'opt_lbl'), $flatten);
	}

//____________________________________________________________________________________
function get_ctx($ctx_id=null)
{
	if (!check_index($ctx_id) ) {return null;}

	# Get the details on the selected context
	$ctx_sql = "Select ctx_name, 
		ctx_lbl, 
		ctx_dsr, 
		ctx_type_id, 
		types.type_name, 
		ctx.spc_ord, 
		ctx.act_bit 
	From ctx, 
		types 
	Where ctx.id = ? and 
		ctx_type_id = types.id";

	$output = array('ctx_name', 'ctx_lbl', 'ctx_dsr', 'ctx_type_id', 'ctx_type_name', 'ctx_spc_ord', 'ctx_act_bit');
	return row_pattern($ctx_sql, 'i', array('id'=>$ctx_id), $output);
	}

//____________________________________________________________________________________
function get_ctx_by_type($ctx_type_id=null, $flatten=false)
{
	$flatten = force_boolean($flatten, false);

	if (!check_index($ctx_type_id) ) {return null;}

	# Get the details on the selected context
	$ctx_sql = "Select ctx.id, 
		ctx_name, 
		ctx_lbl, 
		ctx_dsr, 
		ctx_type_id, 
		types.type_name, 
		ctx.spc_ord, 
		ctx.act_bit, 
		CONCAT_WS('', ctx_name, ' (', ctx.id, ') ', '[', ctx_dsr, '] ') as opt_lbl 
	From ctx, 
		types 
	Where ctx_type_id = ? and 
		ctx_type_id = types.id 
	Order By ctx.spc_ord, 
		ctx.ctx_name";

	$output = array('ctx_id', 'ctx_name', 'ctx_lbl', 'ctx_dsr', 'ctx_type_id', 'ctx_type_name', 'ctx_spc_ord', 'ctx_act_bit', 'ctx_opt_lbl');

	if ($flatten) {$call = 'col_pattern';} else {$call = 'tcol_pattern';}

	return $call($ctx_sql, 'i', array('id'=>$ctx_type_id), $output, $flatten);
	}

//____________________________________________________________________________________
function create_ctx($ctx_name=null, $ctx_lbl=null, $ctx_dsr=null, $spc_ord=null, $ctx_type_id=31)
{
	if (empty($ctx_name) || empty($ctx_lbl) ) {return null;}
	if (empty($ctx_dsr) ) {$ctx_dsr = null;}
	if (empty($spc_ord) || !check_index($spc_ord) ) {$spc_ord = null;}
	if (!check_index($ctx_type_id) ) {$ctx_type_id = 31;}

	# Create a new context
	$insert_sql = "Insert Ignore Into ctx (spc_ord, 
		ctx_name, 
		ctx_dsr, 
		ctx_lbl, 
		ctx_type_id) 
	Values (?, ?, ?, ?, ?)";

	$input = array('so'=>$spc_ord, 
						'name'=>mb_prepstr($ctx_name, 31), 
						'dsr'=>mb_prepstr($ctx_dsr, 127), 
						'lbl'=>mb_prepstr($ctx_lbl, 31), 
						'type'=>$ctx_type_id);

	return ins_pattern($insert_sql, 'isssi', $input);
	}

//____________________________________________________________________________________
function update_ctx($id=null, $ctx_name=null, $ctx_lbl=null, $ctx_dsr=null, $spc_ord=null)
{
	if (empty($ctx_name) || empty($ctx_lbl) ) {return null;}
	if (empty($ctx_dsr) ) {$ctx_dsr = null;}
	if (!check_index($id) ) {return null;}
	if (!check_index($spc_ord) ) {$spc_ord = null;}

	# Update the selected context
	$update_sql = "Update ctx 
	Set 
		ctx_name = ?, 
		ctx_dsr = ?, 
		ctx_lbl = ?, 
		spc_ord = ? 
	Where 
		id = ?";

	$input = array('name'=>mb_prepstr($ctx_name, 31), 
						'dsr'=>mb_prepstr($ctx_dsr, 127), 
						'lbl'=>mb_prepstr($ctx_lbl, 31), 
						'so'=>$spc_ord, 
						'id'=>$id);

	return ins_pattern($update_sql, 'sssii', $input);
	}

//____________________________________________________________________________________
function force_ctx_nulls()
{
	# Set any 0 spc_ord to null
	$update_0_sql = "Update ctx 
	Set spc_ord = NULL 
	Where spc_ord = 0";

	$upd_0 = ins_pattern($update_0_sql);

	# Set any blank description to null
	$update_dsr_sql = "Update ctx 
	Set ctx_dsr = NULL 
	Where ctx_dsr = ''";

	$upd_dsr = ins_pattern($update_dsr_sql);

	return array($upd_0, $upd_dsr);
	}

//____________________________________________________________________________________
function update_ctx_spc_ord($id=null, $spc_ord=null)
{
	if (!check_index($id) ) {return null;}
	if (empty($spc_ord) || !check_index($spc_ord) ) {$spc_ord = null;}

	$upd_sql = "Update ctx 
	Set spc_ord = ? 
	Where id = ?";

	return ins_pattern($upd_sql, 'ii', array('ord'=>$spc_ord, 'id'=>$id) );
	}

//____________________________________________________________________________________
function update_ctx_act_bit($id=null, $act_bit=true)
{
	if (!check_index($id) ) {return null;}
	$act_bit = force_boolean($act_bit, true);

	$upd_sql = "Update ctx
	Set act_bit = ? 
	Where id = ?";

	return ins_pattern($upd_sql, 'ii', array('bit'=>$act_bit, 'id'=>$id) );
	}

//____________________________________________________________________________________
function get_act_ctx($pg_ctxs=false)
{
	// We normally want to exclude page contexts because we are dealing with object values
	$pg_ctxs = force_boolean($pg_ctxs, false);

	if (!$pg_ctxs)
		{$extra = " and ctx_type_id <> 36 ";}
	
	// Get the active contexts
	$sql = "Select id, 
		ctx_name, 
		ctx_lbl, 
		ctx_dsr, 
		ctx_type_id, 
		spc_ord 
	From ctx 
	Where act_bit $extra 
	Order By spc_ord, 
		ctx_name";

	return tcol_pattern($sql, null, null, array('ctx_id', 'ctx_name', 'ctx_lbl', 'ctx_dsr', 'ctx_type_id', 'ctx_spc_ord') );
	}

//____________________________________________________________________________________



?>
