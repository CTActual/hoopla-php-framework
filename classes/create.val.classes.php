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

// Classes and functions for creating and object values

//____________________________________________________________________________________
function get_val($pg_obj_id=null, $pg_obj_set_type_id=null, $pg_id=null, $ctx_id=1)
{
	if (!check_index($pg_obj_id) || !check_index($pg_obj_set_type_id) || !check_index($ctx_id) ) {return null;}

	$str = 'iii';
	$input = array($pg_obj_id, $pg_obj_set_type_id, $ctx_id);
	$extra = "";

	if ($pg_id === NULL)
	{
		$extra = " and pg_id IS NULL ";
		}
	elseif (check_index($pg_id) )
	{
		$str .= 'i';
		$input[] = $pg_id;
		$extra = " and pg_id = ? ";
		}
	elseif (!check_index($pg_id) ) {return null;}

	// Get either the page value or the default (no page) value
	$sql = "Select id, 
		pg_obj_set_val, 
		act_bit 
	From pg_obj_pg_obj_set_val_brg 
	Where pg_obj_id = ? and 
		pg_obj_set_type_id = ? and 
		ctx_id = ? 
		$extra 
	Limit 1";
	
	return row_pattern($sql, $str, $input, array('val_id', 'val', 'val_act_bit') );
	}

//____________________________________________________________________________________
function get_vals_by_pg_and_obj($pg_id=null, $pg_obj_id=null)
{
	if (!check_index($pg_id) || !check_index($pg_obj_id) ) {return null;}

	# Get the all values for a page/object combo so they can be listed out
	$sql = "Select p.id, 
		p.pg_obj_set_type_id, 
		p.pg_obj_set_val, 
		p.ctx_id, 
		p.act_bit, 
		t1. type_name, 
		t1.std_type_lbl, 
		c.ctx_name, 
		c.ctx_lbl, 
		c.ctx_type_id, 
		t2.type_name, 
		t2.std_type_lbl 
	From pg_obj_pg_obj_set_val_brg as p, 
		types as t1, 
		types as t2, 
		ctx as c 
	Where p.pg_id = ? and 
		p.pg_obj_id = ? and 
		p.pg_obj_set_type_id = t1.id and 
		p.ctx_id = c.id and 
		c.ctx_type_id = t2.id and 
		t1.act_bit and 
		t2.act_bit and 
		c.act_bit 
	Order By c.spc_ord, 
		t1.spc_ord";

	$output = array('val_id', 
							'val_pg_obj_set_type_id', 
							'val', 
							'val_ctx_id', 
							'val_act_bit', 
							'val_set_type_name', 
							'val_set_type_lbl', 
							'val_ctx_name', 
							'val_ctx_lbl', 
							'val_ctx_type_id', 
							'val_ctx_type_name', 
							'val_ctx_type_lbl');

	return tcol_pattern($sql, 'ii', array('pg'=>$pg_id, 'obj'=>$pg_obj_id), $output);
	}

//____________________________________________________________________________________
function get_pg_val_set_type_ids($pg_id=null, $pg_obj_id=null)
{
	if (!check_index($pg_id) || !check_index($pg_obj_id) ) {return null;}

	# Get the distinct page or default object setting types for marking
	$sql = "Select Distinct p.pg_obj_set_type_id 
	From pg_obj_pg_obj_set_val_brg as p, 
		types as t1, 
		types as t2, 
		ctx as c 
	Where (p.pg_id = ? or p.pg_id Is Null) and 
		p.pg_obj_id = ? and 
		p.pg_obj_set_type_id = t1.id and 
		p.pg_obj_set_val Is Not Null and 
		p.pg_obj_set_val != '' and 
		p.ctx_id = c.id and 
		c.ctx_type_id = t2.id and 
		t1.act_bit and 
		t2.act_bit and 
		c.act_bit";

	$output = array('val_pg_obj_set_type_id');

	return col_pattern($sql, 'ii', array('pg'=>$pg_id, 'obj'=>$pg_obj_id), $output);
	}
	
//____________________________________________________________________________________
function evaluate_entries($pg_obj_id=null, $pg_obj_set_type_id=null, $pg_id=null, $ctx_id=1, $val_id=null, $active=null, $act_bit=null, $val=null)
{
	if (!check_indexes(array($pg_obj_id, $pg_obj_set_type_id, $ctx_id) ) ) {return null;}

	// See if this is an update by checking the $val_id or a new entry if NULL
	if (check_index($val_id) )
	{
		// We need some indication of a prior state for act_bit to proceed
		// When checked, $act_bit returns $val_id
		if ($active != 'true' && $active != 'false' && $act_bit != $val_id) {return null;}

		// Since nothing will be returned if not checked, we need to check the prior state to declare $act_bit false
		if ($active == 'false' && $act_bit != $val_id) {$act_bit = false;}
		elseif ($active == 'true' && $act_bit != $val_id) {$act_bit = false;}
		elseif ($act_bit == $val_id) {$act_bit = true;}	# We either turn it true or keep it true
		else {$act_bit = false;}

		return update_val($val_id, $act_bit, $val);
		}
	elseif ($val_id === NULL)
	{
		// See if this is a page specific value or not
		if (check_index($pg_id) )
		{
			return create_pg_val($pg_obj_id, $pg_obj_set_type_id, $pg_id, $ctx_id, true, $val);
			}
		elseif ($pg_id === NULL)
		{
			return create_def_val($pg_obj_id, $pg_obj_set_type_id, $ctx_id, true, $val);
			}
		}
	return null;
	}

//____________________________________________________________________________________
function create_def_val($pg_obj_id=null, $pg_obj_set_type_id=null, $ctx_id=1, $act_bit=true, $val=null)
{
	if (!check_indexes(array($pg_obj_id, $pg_obj_set_type_id, $ctx_id) ) ) {return null;}
	$act_bit = force_boolean($act_bit, true);
	$val = ($val !== '0' && $val !== 0 && empty($val) ) ? null : $val;

	# Create a new default (pageless) object value
	# ctx_id is never null, the default is 1
	$sql = "Insert Into pg_obj_pg_obj_set_val_brg (pg_obj_id, 
		pg_obj_set_type_id, 
		pg_obj_set_val, 
		ctx_id, 
		act_bit) 
	Values (?, ?, ?, ?, ?)";

	$input = array('obj'=>$pg_obj_id, 'set'=>$pg_obj_set_type_id, 'val'=>$val, 'ctx'=>$ctx_id, 'bit'=>$act_bit);

	return ins_pattern($sql, 'iisii', $input);
	}

//____________________________________________________________________________________
function create_pg_val($pg_obj_id=null, $pg_obj_set_type_id=null, $pg_id=null, $ctx_id=1, $act_bit=true, $val=null)
{
	if (!check_indexes(array($pg_obj_id, $pg_obj_set_type_id, $ctx_id, $pg_id) ) ) {return null;}
	$act_bit = force_boolean($act_bit, true);
	$val = ($val !== '0' && $val !== 0 && empty($val) ) ? null : $val;

	# Create a new page (not pageless) object value
	# ctx_id is never null, the default is 1
	$sql = "Insert Ignore Into pg_obj_pg_obj_set_val_brg (pg_obj_id, 
		pg_obj_set_type_id, 
		pg_obj_set_val, 
		pg_id, 
		ctx_id, 
		act_bit) 
	Values (?, ?, ?, ?, ?, ?)";

	$input = array('obj'=>$pg_obj_id, 'set'=>$pg_obj_set_type_id, 'val'=>$val, 'pg'=>$pg_id, 'ctx'=>$ctx_id, 'bit'=>$act_bit);

	return ins_pattern($sql, 'iisiii', $input);
	}

//____________________________________________________________________________________
function update_def_val($val_id=null, $pg_obj_id=null, $pg_obj_set_type_id=null, $ctx_id=1, $act_bit=true, $val=null)
{
	if (!check_indexes(array($val_id, $pg_obj_id, $pg_obj_set_type_id, $ctx_id) ) ) {return null;}
	$act_bit = force_boolean($act_bit, true);
	$val = ($val !== '0' && $val !== 0 && empty($val) ) ? null : $val;

	# Update a default (pageless) object value
	# We can modify anything, but ctx_id cannot be less than 1
	$sql = "Update pg_obj_pg_obj_set_val_brg 
	Set pg_obj_id = ?, 
		pg_obj_set_type_id = ?, 
		pg_obj_set_val = ?, 
		ctx_id = ?, 
		act_bit = ? 
	Where id = ?";

	$input = array('obj'=>$pg_obj_id, 'set'=>$pg_obj_set_type_id, 'val'=>$val, 'ctx'=>$ctx_id, 'bit'=>$act_bit, 'id'=>$val_id);

	return ins_pattern($sql, 'iisiii', $input);
	}

//____________________________________________________________________________________
function update_pg_val($val_id=null, $pg_obj_id=null, $pg_obj_set_type_id=null, $pg_id=null, $ctx_id=1, $act_bit=true, $val=null)
{
	if (!check_indexes(array($val_id, $pg_obj_id, $pg_obj_set_type_id, $ctx_id, $pg_id) ) ) {return null;}
	$act_bit = force_boolean($act_bit, true);
	$val = ($val !== '0' && $val !== 0 && empty($val) ) ? null : $val;

	# Update a paged (not pageless) object value
	# We can modify anything, but ctx_id cannot be less than 1, nor pg_id
	$sql = "Update pg_obj_pg_obj_set_val_brg 
	Set pg_obj_id = ?, 
		pg_obj_set_type_id = ?, 
		pg_obj_set_val = ?, 
		pg_id = ?, 
		ctx_id = ?, 
		act_bit = ? 
	Where id = ?";

	$input = array('obj'=>$pg_obj_id, 'set'=>$pg_obj_set_type_id, 'val'=>$val, 'pg'=>$pg_id, 'ctx'=>$ctx_id, 'bit'=>$act_bit, 'id'=>$val_id);

	return ins_pattern($sql, 'iisiiii', $input);
	}

//____________________________________________________________________________________
function update_val($val_id=null, $act_bit=true, $val=null)
{
	if (!check_index($val_id) ) {return null;}
	$act_bit = force_boolean($act_bit, true);
	$val = ($val !== '0' && $val !== 0 && empty($val) ) ? null : $val;

	# This is the sensible alternative to the complete queries above
	# We just modify the act_bit and value
	$sql = "Update pg_obj_pg_obj_set_val_brg 
	Set pg_obj_set_val = ?, 
		act_bit = ? 
	Where id = ?";

	$input = array('val'=>$val, 'bit'=>$act_bit, 'id'=>$val_id);

	return ins_pattern($sql, 'sii', $input);
	}

//____________________________________________________________________________________
function det_num_of_set_types_per_obj($obj_id=null, $pg_id=null)
{
	if (!check_indexes(array($obj_id, $pg_id) ) ) {return null;}

	# Count the number of distinct setting types for a obj-pg val combo
	$sql = "Select count(id) From 
				(Select Distinct pg_obj_set_type_id as id
				From pg_obj_pg_obj_set_val_brg 
				Where (pg_id = ? or pg_id Is NULL) and 
					pg_obj_id = ?) as di";

	return row_pattern($sql, 'ii', array('pg'=>$pg_id, 'obj'=>$obj_id), array('count') );
	}

//____________________________________________________________________________________
function chk_for_def_vals($pg_obj_id=null)
{
	if (!check_index($pg_obj_id) ) {return null;}
	
	$sql = "Select c.ctx_name, 
		c.ctx_lbl, 
		c.id, 
		t.type_name, 
		t.std_type_lbl, 
		t.id, 
		p.pg_obj_set_val 
	From pg_obj_pg_obj_set_val_brg as p, 
		types as t, 
		ctx as c 
	Where p.pg_id Is NULL and 
		p.pg_obj_id = ? and 
		p.pg_obj_set_val Is Not NULL and 
		p.ctx_id = c.id and 
		p.pg_obj_set_type_id = t.id and 
		p.act_bit and 
		c.act_bit and 
		t.act_bit 
	Order By c.spc_ord, 
		c.ctx_lbl";

	return tcol_pattern($sql, 'i', array('obj'=>$pg_obj_id), array('def_ctx_name', 'def_ctx_lbl', 'def_ctx_id', 'def_type_name', 'def_type_std_lbl', 'def_type_id', 'def_val') );
	}

//____________________________________________________________________________________
function clean_up_null_vals()
{
	$query = "Delete From pg_obj_pg_obj_set_val_brg 
		Where pg_obj_set_val Is NULL";
		
	return del_pattern($query);
	}
	
//____________________________________________________________________________________


?>
