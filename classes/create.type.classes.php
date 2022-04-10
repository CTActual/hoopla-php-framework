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

// Classes and functions for creating and modifying types

//____________________________________________________________________________________
function get_type($id=null)
{
	if (!check_index($id) ) {return null;}

	# Get details on a specific type
	$query = "Select spc_ord, 
		type_name, 
		std_type_lbl, 
		type_dsr, 
		meta_type_id, 
		act_bit 
	From types 
	Where id = ? 
	Limit 1";

	return row_pattern($query, 'i', array('id'=>$id), array('type_spc_ord', 'type_name', 'std_type_lbl', 'type_dsr', 'meta_type_id', 'type_act_bit') );
	}

//____________________________________________________________________________________
function get_meta_type_list($flatten=true)
{
	$flatten = force_boolean($flatten, true);

	# Get the meta types to list
	$meta_types_sql = "Select id, 
		spc_ord, 
		meta_type_name, 
		meta_type_dsr, 
		CONCAT_WS('', meta_type_name, ' (', id, ') [', meta_type_dsr, '] ') as meta_type_lbl 
	From meta_types 
	Order by spc_ord, meta_type_name";

	$output = array('meta_type_id', 
						'meta_type_spc_ord', 
						'meta_type_name', 
						'meta_type_dsr', 
						'meta_type_lbl');

	return col_pattern($meta_types_sql, null, null, $output, $flatten);
	}

//____________________________________________________________________________________
function get_types($meta_type_id=null)
{
	if (!check_index($meta_type_id) ) {return null;}

	# Get all the existing types of the selected meta-type
	$types_sql = "Select id, 
		spc_ord, 
		type_name, 
		std_type_lbl, 
		type_dsr, 
		meta_type_id, 
		act_bit 
	From types 
	Where meta_type_id = ? 
	Order By spc_ord, 
		type_name";

	$output = array('type_id', 
						'type_spc_ord', 
						'type_name', 
						'std_type_lbl', 
						'type_dsr', 
						'meta_type_id', 
						'type_act_bit');

	return tcol_pattern($types_sql, 'i', array('id'=>$meta_type_id), $output);
	}

//____________________________________________________________________________________
function get_meta_type($meta_type_id=null)
{
	if (!check_index($meta_type_id) ) {return null;}

	# Get the meta type info
	$meta_types_sql = "Select spc_ord, 
		meta_type_name, 
		meta_type_dsr 
	From meta_types 
	Where id = ?";

	$output = array('meta_type_spc_ord', 
						'meta_type_name', 
						'meta_type_dsr');

	return row_pattern($meta_types_sql, 'i', array('id'=>$meta_type_id), $output);
	}

//____________________________________________________________________________________
function enforce_type_nulls()
{
	// Set fields to null instead of some other empty value
	$update_0_sql = "Update types 
	Set spc_ord = NULL 
	Where spc_ord = 0";

	$update_lbl_sql = "Update types 
	Set std_type_lbl = null 
	Where std_type_lbl = ''";

	$update_dsr_sql = "Update types 
	Set type_dsr = NULL 
	Where type_dsr = ''";

	$spc_ord = ins_pattern($update_0_sql);
	$std_lbl = ins_pattern($update_lbl_sql);
	$dsr = ins_pattern($update_dsr_sql);

	return array($spc_ord, $std_lbl, $dsr);
	}

//____________________________________________________________________________________
function create_type($type_name=null, $meta_type_id=null, $std_type_lbl=null, $type_dsr=null, $spc_ord=null)
{
	if (empty($type_name) || !check_index($meta_type_id) ) {return null;}
	$type_name = mb_prepstr2($type_name, 255);
	if (empty($std_type_lbl) ) {$std_type_lbl = null;} else {$std_type_lbl = mb_prepstr2($std_type_lbl, 63);}
	if (empty($type_dsr) ) {$type_dsr = null;} else {$type_dsr = mb_prepstr2($type_dsr, 1023);}
	if (!check_index($spc_ord) ) {$spc_ord = null;}	

	# Create a new type
	$insert_sql = "Insert Ignore Into types (spc_ord, 
		type_name, 
		std_type_lbl, 
		type_dsr, 
		meta_type_id) 
	Values (?, ?, ?, ?, ?)";

	return ins_pattern($insert_sql, 'isssi', array('so'=>$spc_ord, 'name'=>$type_name, 'lbl'=>$std_type_lbl, 'dsr'=>$type_dsr, 'mtid'=>$meta_type_id) );
	}

//____________________________________________________________________________________
function update_type($type_id=null, $type_name=null, $std_type_lbl=null, $type_dsr=null, $spc_ord=null)
{
	if (empty($type_name) || !check_index($type_id) ) {return null;}
	$type_name = mb_prepstr2($type_name, 255);
	if (empty($std_type_lbl) ) {$std_type_lbl = null;} else {$std_type_lbl = mb_prepstr2($std_type_lbl, 63);}
	if (empty($type_dsr) ) {$type_dsr = null;} else {$type_dsr = mb_prepstr2($type_dsr, 1023);}
	if (!check_index($spc_ord) ) {$spc_ord = null;}

	# Update a type
	$update_sql = "Update types 
	Set type_name = ?, 
		std_type_lbl = ?, 
		type_dsr = ?, 
		spc_ord = ? 
	Where id = ?";

	return ins_pattern($update_sql, 'sssii', array('name'=>$type_name, 'lbl'=>$std_type_lbl, 'dsr'=>$type_dsr, 'so'=>$spc_ord, 'id'=>$type_id) );
	}

//____________________________________________________________________________________
function update_type_spc_ord($id=null, $spc_ord=null)
{
	if (!check_index($id) ) {return null;}
	if (empty($spc_ord) || !check_index($spc_ord) ) {$spc_ord = null;}

	$upd_sql = "Update types 
	Set spc_ord = ? 
	Where id = ?";

	return ins_pattern($upd_sql, 'ii', array('ord'=>$spc_ord, 'id'=>$id) );
	}

//____________________________________________________________________________________
function update_type_act_bit($id=null, $act_bit=true)
{
	if (!check_index($id) ) {return null;}
	$act_bit = force_boolean($act_bit, true);

	$upd_sql = "Update types
	Set act_bit = ? 
	Where id = ?";

	return ins_pattern($upd_sql, 'ii', array('bit'=>$act_bit, 'id'=>$id) );
	}

//____________________________________________________________________________________


//____________________________________________________________________________________



?>
