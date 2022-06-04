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

// Classes and functions for creating and modifying page objects

//____________________________________________________________________________________
function sel_pg_obj($obj_id=null)
{
	if (!check_index($obj_id) ) {return null;}

	# Get the info on a single obj
	$sel_obj_sql = "Select obj_name, 
		obj_dsr, 
		acs_str, 
		act_bit, 
		pg_obj_type_id 
	From pg_objs 
	Where pg_objs.id = ? 
	Limit 1";

	return row_pattern($sel_obj_sql, 'i', array('obj_id'=>$obj_id), array('sel_obj_name', 'sel_obj_dsr', 'sel_acs_str', 'sel_act_bit', 'sel_pg_obj_type_id') );
	}

//____________________________________________________________________________________
function get_pg_obj_list($type_id=null)
{
	if (!check_index($type_id) ) {return null;}

	# Get info on all objs of the same type
	$obj_sql = "Select obj_name, 
		obj_dsr, 
		acs_str, 
		id, 
		act_bit 
	From pg_objs 
	Where pg_obj_type_id = ? 
	Order By obj_name";

	return tcol_pattern($obj_sql, 'i', array('id'=>$type_id), array(	'obj_name', 
																					'obj_dsr', 
																					'acs_str', 
																					'obj_id', 
																					'act_bit') );
	}

//____________________________________________________________________________________
function get_full_pg_obj_list($flatten=true)
{
	$flatten = force_boolean($flatten, true);

	# Get info on all active objs assigned to at least one page
	$obj_sql = "Select pg_objs.obj_name, 
		pg_objs.pg_obj_type_id, 
		types.type_name, 
		types.spc_ord, 
		pg_objs.obj_dsr, 
		pg_objs.acs_str, 
		pg_objs.id, 
		CONCAT_WS('', obj_name, ' (', pg_objs.id, '), ', type_name, ' &mdash; ', obj_dsr) as list 
	From pg_objs, 
		types 
	Where pg_objs.act_bit and 
		pg_objs.pg_obj_type_id = types.id and 
		types.act_bit and 
		pg_objs.id In (Select pg_obj_id From pg_pg_obj_brg Where act_bit) 
	Order By types.spc_ord, 
		pg_objs.obj_name";

	return col_pattern($obj_sql, null, null, array(	'obj_name', 
																'obj_type_id', 
																'obj_type_name', 
																'obj_type_spc_ord', 
																'obj_dsr', 
																'acs_str', 
																'obj_id', 
																'obj_list_lbl'), $flatten);
	}

//____________________________________________________________________________________
function get_non_pg_obj_ids()
{
	# Get all obj ids to loop through
	$all_pg_obj_ids_sql = "Select id 
	From pg_objs 
	Where pg_obj_type_id != 14 
	Order by id";

	return col_pattern($all_pg_obj_ids_sql, null, null, array('non_pg_obj_ids') );
	}

//____________________________________________________________________________________
function create_pg_obj($obj_type_id=null, $obj_name=null, $obj_dsr=null, $acs_str=null)
{
	if (!check_index($obj_type_id) || empty($obj_name) ) {return null;}

	# Create a new obj
	$insert_sql = "Insert Ignore Into pg_objs 
		(pg_obj_type_id, 
		obj_name, 
		obj_dsr, 
		acs_str, 
		act_bit) 
	Values (?, ?, ?, ?, true)";

	$bind_array_3 = array(	'pg_obj_type_id'=>(int) $obj_type_id, 
									'obj_name'=>mb_prepstr($obj_name, 63), 
									'obj_dsr'=>mb_prepstr($obj_dsr, 255), 
									'acs_str'=>mb_prepstr($acs_str, 255) );

	return ins_pattern($insert_sql, 'isss', $bind_array_3);
	}

//____________________________________________________________________________________
function copy_pg_objs($pg_id=null, $pg_obj_id=null, $pg_loc=null, $spc_ord=null, $use_def_bit=true)
{
	if (!check_index($pg_id) || !check_index($pg_obj_id) ) {return null;}
	if (!check_index($spc_ord) ) {$spc_ord = null;}
	if (empty($pg_loc) ) {$pg_loc = null;}
	$use_def_bit = force_boolean($use_def_bit, true);

	# Copy objects from one page to another
	$insert_copy_sql = "Insert Into pg_pg_obj_brg 
		(pg_id, 
		pg_obj_id, 
		pg_obj_loc, 
		spc_ord, 
		use_def_bit, 
		act_bit) 
	Values (?, ?, ?, ?, ?, true) 
	ON DUPLICATE KEY UPDATE act_bit = true";

	$bind_array_5 = array(	'pg_id'=>(int) $pg_id, 
									'pg_obj_id'=>$pg_obj_id, 
									'loc'=>$pg_loc, 
									'so'=>$spc_ord,  
									'udb'=>$use_def_bit);

	return ins_pattern($insert_copy_sql, 'iisii', $bind_array_5);
	}

//____________________________________________________________________________________
function update_pg_obj($pg_obj_id=null, $obj_name=null, $obj_dsr=null, $acs_str=null)
{
	if (!check_index($pg_obj_id) || empty($obj_name) ) {return null;}

	$query = "Update pg_objs 
		Set obj_name = ?, 
			obj_dsr = ?, 
			acs_str = ? 
		Where id = ?";

	$input = array(	'obj_name'=>mb_prepstr($obj_name, 63), 
							'obj_dsr'=> (!empty($obj_dsr) ) ? mb_prepstr($obj_dsr, 255) : null, 
							'acs_str'=> (!empty($acs_str) ) ? mb_prepstr($acs_str, 255) : null, 
							'id'=>(int) trim($pg_obj_id) );

	return ins_pattern($query, 'sssi', $input);
	}

//____________________________________________________________________________________
function set_pg_obj_tf($pg_obj_id=null, $tf=true)
{
	if (!check_index($pg_obj_id) ) {return null;}

	$tf = force_boolean($tf, true);

	$query = "Update pg_objs 
				Set act_bit = ? 
				Where id = ?";

	return ins_pattern($query, 'ii', array('act_bit'=>$tf, 'id'=>$pg_obj_id) );
	}

//____________________________________________________________________________________
function clean_up_pg_obj_nulls()
{
	# Clean up fields with nulls
	$null_dsr_sql = "Update pg_objs 
	Set obj_dsr = null 
	Where obj_dsr = ''";

	$null_acs_str_sql = "Update pg_objs 
	Set acs_str = null 
	Where acs_str = ''";

	$output1 = ins_pattern($null_dsr_sql);
	$output2 = ins_pattern($null_acs_str_sql);

	return array($output1, $output2);
	}

//____________________________________________________________________________________
function pg_obj_type_list($flatten=true, $exclude_pgs=true)
{
	$flatten = force_boolean($flatten, true);
	$exclude_pgs = force_boolean($exclude_pgs, true);

	if ($exclude_pgs) {$extra = "and types.id != 14 ";} else {$extra = "";}

	# Get a list of the obj types (excluding pages)
	$obj_types_sql = "Select id, 
		CONCAT_WS('', type_name, ' [', std_type_lbl, '] (', id, ') obj count: ', cnt) as lbl 
	From 
		(Select types.id, 
			types.type_name, 
			types.std_type_lbl, 
			(Select Count(id) From pg_objs Where pg_obj_type_id = types.id) as cnt 
		From types 
		Where meta_type_id = 1 $extra
		Order by spc_ord) as obj_types";

	return col_pattern($obj_types_sql, null, null, array('type_id', 'type_lbl'), $flatten);
	}

//____________________________________________________________________________________
function update_pg_pg_obj_brg($pg_id=null, $pg_obj_id=null, $obj_loc=null, $spc_ord=null, $tf=true, $udb=true)
{
	$result1 = update_obj_loc($pg_id, $pg_obj_id, $obj_loc);
	$result2 = update_obj_spc_ord($pg_id, $pg_obj_id, $spc_ord);
	$result3 = set_pg_pg_obj_brg_act_bit($pg_id, $pg_obj_id, $tf);
	$result4 = set_pg_pg_obj_brg_use_def_bit($pg_id, $pg_obj_id, $udb);

	return array($result1, $result2, $result3, $result4);
	}

//____________________________________________________________________________________
//____________________________________________________________________________________
function set_pg_pg_obj_brg_act_bit($pg_id=null, $pg_obj_id=null, $tf=true)
{
	if (!check_index($pg_id) || !check_index($pg_obj_id) ) {return null;}
	$tf = force_boolean($tf, true);

	# Set all bridge records to false before final update
	$update_sql = "Update pg_pg_obj_brg 
	Set act_bit = ? 
	Where pg_id = ? and 
		pg_obj_id = ?";

	return ins_pattern($update_sql, 'iii', array('bit'=>$tf, 'pg'=>$pg_id, 'obj'=>$pg_obj_id) );
	}

//____________________________________________________________________________________
function set_pg_pg_obj_brg_act_bit_false($pg_id=null, $exclude_pgs=true)
{
	if (!check_index($pg_id) ) {return null;}
	$exclude_pgs = force_boolean($exclude_pgs, true);

	if ($exclude_pgs) {$extra = "and pg_obj_id Not IN (Select pg_obj_id From pgs) ";} else {$extra = "";}

	# Set all bridge records to false before final update
	$update_sql = "Update pg_pg_obj_brg 
	Set act_bit = false 
	Where pg_id = ? and 
		act_bit = true $extra";

	return ins_pattern($update_sql, 'i', array('id'=>$pg_id) );
	}

//____________________________________________________________________________________
function set_pg_pg_obj_brg_act_bit_true($pg_id=null, $pg_obj_id=null)
{
	if (!check_index($pg_id) || !check_index($pg_obj_id) ) {return null;}

	# Set all bridge records that are true to true
	$insert_sql = "Insert Into pg_pg_obj_brg 
		(pg_id, 
		pg_obj_id, 
		act_bit) 
	Values (?, ?, true) 
	ON DUPLICATE KEY UPDATE act_bit = true";

	return ins_pattern($insert_sql, 'ii', array('id'=>$pg_id, 'obj_id'=>$pg_obj_id) );
	}

//____________________________________________________________________________________
//____________________________________________________________________________________
function set_pg_pg_obj_brg_use_def_bit($pg_id=null, $pg_obj_id=null, $tf=true)
{
	if (!check_index($pg_id) || !check_index($pg_obj_id) ) {return null;}
	$tf = force_boolean($tf, true);

	# Set all bridge records to false before final update
	$update_sql = "Update pg_pg_obj_brg 
	Set use_def_bit = ? 
	Where pg_id = ? and 
		pg_obj_id = ?";

	return ins_pattern($update_sql, 'iii', array('bit'=>$tf, 'pg'=>$pg_id, 'obj'=>$pg_obj_id) );
	}

//____________________________________________________________________________________
function set_pg_pg_obj_brg_use_def_bit_false($pg_id=null, $exclude_pgs=true)
{
	if (!check_index($pg_id) ) {return null;}
	$exclude_pgs = force_boolean($exclude_pgs, true);

	if ($exclude_pgs) {$extra = "and pg_obj_id Not IN (Select pg_obj_id From pgs) ";} else {$extra = "";}

	# Set all bridge records to false before final update
	$update_sql = "Update pg_pg_obj_brg 
	Set use_def_bit = false 
	Where pg_id = ? and 
		use_def_bit = true $extra";

	return ins_pattern($update_sql, 'i', array('id'=>$pg_id) );
	}

//____________________________________________________________________________________
//____________________________________________________________________________________
function update_obj_loc($pg_id=null, $pg_obj_id=null, $obj_loc=null)
{
	if (!check_index($pg_id) || !check_index($pg_obj_id) ) {return null;}
	$obj_loc = (!empty($obj_loc) ) ? mb_prepstr2($obj_loc, 255) : null;

	# Update all loc fields
	$update_loc_sql = "Update pg_pg_obj_brg 
	Set pg_obj_loc = IF(STRCMP('',?), ?, NULL) 
	Where pg_id = ? and 
		pg_obj_id = ?";

	return ins_pattern($update_loc_sql, 'ssii', array('obj_loc1'=>$obj_loc, 'obj_loc2'=>$obj_loc, 'id'=>$pg_id, 'obj_id'=>$pg_obj_id) );
	}

//____________________________________________________________________________________
function update_obj_spc_ord($pg_id=null, $pg_obj_id=null, $spc_ord=null)
{
	if (!check_index($pg_id) || !check_index($pg_obj_id) ) {return null;}
	if (!check_index($spc_ord) ) {$spc_ord = null;}

	# Update spc_ord field
	$update_spc_ord_sql = "Update pg_pg_obj_brg 
	Set spc_ord = ? 
	Where pg_id = ? and 
		pg_obj_id = ?";

	return ins_pattern($update_spc_ord_sql, 'iii', array('spc_ord'=>$spc_ord, 'id'=>$pg_id, 'obj_id'=>$pg_obj_id) );
	}

//____________________________________________________________________________________
function append_obj_spc_ord($pg_id=null, $pg_obj_id=null)
{
	if (!check_index($pg_id) || !check_index($pg_obj_id) ) {return null;}

	# Append to spc_ord field
	$query = "Select IfNull(max(spc_ord) + 1, 1) From pg_pg_obj_brg Where pg_id = ? Limit 1";

	$max_spc_ord = row_pattern($query, 'i', array('pg_id'=>$pg_id), array('mso') );

	update_obj_spc_ord($pg_id, $pg_obj_id, $max_spc_ord);
	}

//____________________________________________________________________________________
function get_pg_obj_set_types($pg_obj_type_id=null)
{
	if (!check_index($pg_obj_type_id) ) {return null;}

	# Get all the current bridge records
	$brg_sql = "Select pg_obj_set_type_id 
	From pg_obj_type_pg_obj_set_type_brg 
	Where pg_obj_type_id = ? and 
		act_bit";

	return col_pattern($brg_sql, 'i', array('id'=>$pg_obj_type_id), array('pg_obj_set_type_id') );
	}

//____________________________________________________________________________________
function update_pg_obj_type_pg_obj_set_type_brg($pg_obj_type_id=null, $pg_obj_set_type_id=null, $act_bit=true)
{
	if (!check_index($pg_obj_type_id) || !check_index($pg_obj_set_type_id) ) {return null;}
	$act_bit = force_boolean($act_bit, true);

	# Update the specified bridge record
	$insert_sql = "Insert Into pg_obj_type_pg_obj_set_type_brg 
		(pg_obj_type_id, 
		pg_obj_set_type_id, 
		act_bit) 
	Values (?, ?, ?) 
	ON DUPLICATE KEY UPDATE act_bit = ?";

	return ins_pattern($insert_sql, 'iiii', array('obj_id'=>$pg_obj_type_id, 'set_id'=>$pg_obj_set_type_id, 'bit1'=>$act_bit, 'bit2'=>$act_bit) );
	}

//____________________________________________________________________________________
function list_pg_obj_set_types($pg_obj_type_id=null, $flatten=true)
{
	if (!check_index($pg_obj_type_id) ) {return null;}
	$flatten = force_boolean($flatten, true);

	# Get all the current bridge records and type info
	$brg_sql = "Select types.id, 
		types.type_name, 
		types.std_type_lbl, 
		types.type_dsr, 
		CONCAT_WS('', type_name, ', ❝', std_type_lbl, '❞ (', types.id, '), ', type_dsr) as type_list
	From pg_obj_type_pg_obj_set_type_brg as potb, 
		types 
	Where pg_obj_type_id = ? and 
		potb.act_bit and 
		types.id = potb.pg_obj_set_type_id and 
		types.act_bit 
	Order By types.spc_ord, 
		types.type_name";

	return col_pattern($brg_sql, 'i', array('id'=>$pg_obj_type_id), array('pg_obj_set_type_id', 'set_type_name', 'set_type_lbl', 'set_type_dsr', 'set_type_list'), $flatten);
	}

//____________________________________________________________________________________
function get_obj_pg_list($obj_id=null)
{
	if (!check_index($obj_id) ) {return null;}

	# Get the list of active pages assigned to the given object
	$sql = "Select pgs.id, 
		po.id, 
		po.obj_name, 
		po.obj_dsr, 
		po.acs_str, 
		po.act_bit, 
		ppob.pg_obj_loc, 
		ppob.spc_ord, 
		ppob.use_def_bit, 
		ppob.act_bit 
	From pg_objs as po, 
		pg_pg_obj_brg as ppob, 
		pgs 
	Where ppob.pg_obj_id = ? and 
		pgs.id = ppob.pg_id and 
		pgs.pg_obj_id = po.id and 
		po.pg_obj_type_id = 14 and 		
		ppob.act_bit 
	Order By po.obj_name";

	return tcol_pattern($sql, 'i', array('id'=>$obj_id), array('pg_id', 'pg_obj_id', 'pg_name', 'pg_dsr', 'pg_acs_str', 'pg_act_bit', 'obj_loc', 'ppob_spc_ord', 'ppob_use_def_bit', 'ppob_act_bit') );
	}
//____________________________________________________________________________________
function clone_pg_objs($src_pg_id=null, $dst_pg_id=null)
{
	// Do not copy page objects to the same page
	if (!check_index($src_pg_id) || !check_index($dst_pg_id) || $src_pg_id == $dst_pg_id) {return null;}
	
	# Clone objects from src to dst, but not the pg as a pg_obj
	$insert_sql = "Insert Ignore Into pg_pg_obj_brg (pg_id, 
		pg_obj_id, 
		pg_obj_loc, 
		spc_ord, 
		use_def_bit, 
		act_bit) 
	Select ?, 
		pg_obj_id, 
		pg_obj_loc, 
		spc_ord, 
		use_def_bit, 
		true 
	From pg_pg_obj_brg 
	Where pg_id = ? and 
		act_bit and 
		pg_obj_id Not In (Select pg_obj_id From pgs Where id = ?) and 
		pg_id In (Select id From pgs Where id = ?)";

	$input = array('dst'=>$dst_pg_id, 'src1'=>$src_pg_id, 'src2'=>$src_pg_id, 'src3'=>$src_pg_id);
	
	return ins_pattern($insert_sql, 'iiii', $input);
	
	}	# End of clone page objects function

//____________________________________________________________________________________
function det_use_def_bit($obj_id=null, $pg_id=null)
{
	if (!check_indexes(array($obj_id, $pg_id) ) ) {return null;}

	# Get the use_def_bit value
	$sql = "Select use_def_bit From 
				pg_pg_obj_brg 
			Where pg_id = ? and 
				pg_obj_id = ? 
			Limit 1";

	return row_pattern($sql, 'ii', array('pg'=>$pg_id, 'obj'=>$obj_id), array('bit') );
	}

//____________________________________________________________________________________


?>
