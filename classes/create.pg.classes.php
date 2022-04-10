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

// Classes and functions for creating pages and modifying objects

function get_pg_list($pg_ctx_id=null)
{
	// Filter by page context id
	if (check_index($pg_ctx_id) )
	{
		$str = 'i';
		$input = array('pg_ctx_id'=>$pg_ctx_id);
		$extra = "and pgs.pg_ctx_id = ? ";
		}
	else
	{
		$str = '';
		$input = array();
		$extra = '';
		}
		
	$obj_sql = "Select pg_objs.id, 
		obj_name, 
		obj_dsr, 
		ppob.pg_obj_loc, 
		ppob.spc_ord, 
		acs_str, 
		pgs.id, 
		pgs.pg_ctx_id, 
		ctx.ctx_name, 
		ctx.ctx_lbl, 
		pg_objs.act_bit 
	From pg_objs, 
		ctx, 
		pgs 
		Left Join 
			(Select pg_obj_loc, 
				spc_ord, 
				pg_id 
			From pg_pg_obj_brg as ippob, 
				pgs, 
				pg_objs 
			Where pg_id = pgs.id and 
				pg_objs.id = ippob.pg_obj_id and 
				pgs.pg_obj_id = pg_objs.id) as ppob On 
			(pg_id = pgs.id) 
	Where pgs.pg_obj_id = pg_objs.id and 
		pgs.pg_ctx_id = ctx.id $extra 
	Order by obj_name, 
		pg_objs.id";

	$result_array_1 = array(	'obj_id', 
									'obj_name', 
									'obj_dsr', 
									'url_tag', 
									'pg_ord', 
									'acs_str', 
									'pg_id', 
									'pg_ctx_id', 
									'pg_ctx_name', 
									'pg_ctx_lbl', 
									'act_bit');

	return tcol_pattern($obj_sql, $str, $input, $result_array_1);
	}

//____________________________________________________________________________________
function get_pg_info($pg_id=null)
{
	if (!check_index($pg_id) ) {return null;}

	$pg_sql = "Select pg_objs.id, 
		obj_name, 
		obj_dsr, 
		ppob.pg_obj_loc, 
		pgs.pg_ctx_id, 
		ctx.ctx_name, 
		ctx.ctx_lbl, 
		acs_str 
	From pg_objs, 
		ctx, 
		pgs 
		Left Join 
			(Select pg_obj_loc, 
				pg_id 
			From pg_pg_obj_brg as ppob, 
				pgs, 
				pg_objs 
			Where pg_id = pgs.id and 
				pg_objs.id = ppob.pg_obj_id and 
				pgs.pg_obj_id = pg_objs.id) as ppob On 
			(pg_id = pgs.id) 
	Where pgs.id = ? and 
		pgs.pg_obj_id = pg_objs.id and 
		pgs.pg_ctx_id = ctx.id ";

	$result_array_1b = array(	'sel_obj_id', 
										'sel_obj_name', 
										'sel_obj_dsr', 
										'sel_obj_loc', 
										'sel_pg_ctx_id', 
										'sel_pg_ctx_name', 
										'sel_pg_ctx_lbl', 
										'sel_acs_str');

	return row_pattern($pg_sql, 'i', array('id'=>$pg_id), $result_array_1b);
	}

//____________________________________________________________________________________
function create_pg($obj_name=null, $obj_dsr=null, $acs_str=null, $url_tag=null, $pg_ctx_id=null)
{
	if (empty($obj_name) ) {return null;}
	if (!check_index($pg_ctx_id) ) {$pg_ctx_id = 2;}

	#  Create the page object first
	$insert_obj_sql = "Insert Ignore Into pg_objs 
		(pg_obj_type_id, 
		obj_name, 
		obj_dsr, 
		acs_str, 
		act_bit) 
	Values (14, ?, ?, ?, true)";

	$type_str_2 = 'sss';
	$bind_array_2 = array(	'obj_name'=>mb_prepstr($obj_name, 63), 
									'obj_dsr'=>mb_prepstr($obj_dsr, 255), 
									'acs_str'=>mb_prepstr($acs_str, 255) );

	$pg_obj_id = ins_pattern($insert_obj_sql, $type_str_2, $bind_array_2);	

	if (!check_index($pg_obj_id) ) {return null;}

	#  Create the page entry next (need the new page obj id)
	$insert_pg_sql = "Insert Ignore Into pgs 
		(pg_obj_id, pg_ctx_id) 
	Values (?, ?)";

	$pg_id = ins_pattern($insert_pg_sql, 'ii', array('pg_obj_id'=>$pg_obj_id, 'pg_ctx_id'=>$pg_ctx_id) );

	if (!check_index($pg_id) ) {return null;}

	#  Create the brg entry next (need the new page obj id and pg_id)
	$insert_pg_brg_sql = "Insert Ignore Into pg_pg_obj_brg 
		(pg_id, 
		pg_obj_id, 
		pg_obj_loc, 
		act_bit) 
	Values (?, ?, ?, true)";

	return ins_pattern($insert_pg_brg_sql, 'iis', array(	'pg_id'=>$pg_id, 
																		'pg_obj_id'=>$pg_obj_id, 
																		'pg_obj_loc'=>mb_prepstr2($url_tag, 255) ) );
	}

//____________________________________________________________________________________
function update_pg_brg($pg_id=null, $pg_obj_id=null, $url_tag=null)
{
	if (!check_index($pg_id) || !check_index($pg_obj_id) ) {return null;}

	$update_pg_brg_sql = "Insert Into pg_pg_obj_brg 
		(pg_id, 
		pg_obj_id, 
		pg_obj_loc, 
		act_bit) 
	Values (?, ?, ?, true) 
	On Duplicate Key Update pg_obj_loc = ?";

	return ins_pattern($update_pg_brg_sql, 'iiss', array(	'pg_id'=>(int) trim($pg_id), 
																		'pg_obj_id'=>(int) trim($pg_obj_id), 
																		'pg_obj_loc1'=>mb_prepstr2($url_tag, 255),
																		'pg_obj_loc2'=>mb_prepstr2($url_tag, 255) ) );	
	}

//____________________________________________________________________________________
function get_std_sets_for_pg_obj_type($pg_obj_type_id=null, $flatten=true)
{
	if (!check_index($pg_obj_type_id) ) {return null;}
	$flatten = force_boolean($flatten, true);

	# All std setting types for a page object type
	$std_set_sql = "Select p.pg_obj_set_type_id, 
		CONCAT_WS('', types.type_name, ' (',  p.pg_obj_set_type_id, ') ') as type_lbl 
	From pg_obj_type_pg_obj_set_type_brg as p, 
		types 
	Where p.pg_obj_type_id = ? and 
		p.act_bit and 
		p.pg_obj_set_type_id =  types.id";

	return col_pattern($std_set_sql, 'i', array('type'=>$pg_obj_type_id),  array('pg_obj_set_type_ids', 'pg_obj_set_type_names'), $flatten);
	}

//____________________________________________________________________________________
function create_pg_dropdown($flatten=true)
{
	$flatten = force_boolean($flatten, true);

	# Get all the pages with names
	$pgs_sql = "Select pgs.id, 
		obj_name, 
		CONCAT_WS('', obj_name, ' (', pgs.id, ') [', ctx.ctx_lbl, '] ', obj_dsr) as pg_dsr 
	From pgs, 
		pg_objs, 
		ctx 
	Where pgs.pg_obj_id = pg_objs.id and 
		pg_objs.act_bit and 
		pgs.pg_ctx_id = ctx.id 
	Order by pg_ctx_id, 
		obj_name";

	return col_pattern($pgs_sql, null, null, array('pg_id', 'pg_name', 'pg_dsr'), $flatten);
	}

//____________________________________________________________________________________
function get_pg_objs($pg_id=null, $filter_objs=false)
{
	if (!check_index($pg_id) ) {return null;}
	$filter_objs = force_boolean($filter_objs, false);
	
	$str = 'i';
	$input = array('pg_id'=>$pg_id);
	$extra = '';
	
	// We can filter out unassigned objs
	if ($filter_objs)
	{
		$extra = "ppob.act_bit = true and ";
		}

	# Get the page objects and their info
	$pg_objs_sql = "Select po.id, 
		po.obj_name, 
		po.obj_dsr, 
		acs_str, 
		po.act_bit, 
		types.type_name, 
		ppob.pg_obj_loc, 
		ppob.spc_ord, 
		ppob.use_def_bit, 
		ppob.act_bit 
	From types, 
		pg_objs as po 
		Left Join 
			(Select pg_obj_id, 
				pg_obj_loc, 
				spc_ord, 
				use_def_bit, 
				act_bit 
			From pg_pg_obj_brg 
			Where pg_id = ?) as ppob
		On (po.id = ppob.pg_obj_id) 
	Where po.pg_obj_type_id != 14 and 
		$extra
		po.pg_obj_type_id = types.id 
	Order by ppob.act_bit DESC, 
		ppob.spc_ord, 
		types.type_name, 	
		po.obj_name";

	$result_array_2 = array('pg_obj_id', 
									'pg_obj_name', 
									'pg_obj_dsr', 
									'pg_obj_acs_str', 
									'pg_obj_act_bit', 
									'pg_obj_type_name', 
									'pg_obj_loc', 
									'pg_obj_spc_ord', 
									'pg_obj_use_def_bit', 
									'pg_obj_brg_act_bit');

	return tcol_pattern($pg_objs_sql, $str, $input, $result_array_2);
	}

//____________________________________________________________________________________
function get_blank_pgs($flatten=true)
{
	$flatten = force_boolean($flatten, true);

	# Get all pages with no objects other than themselves
	$blank_pg_sql = "Select pgs.id, 
		po1.obj_name 
	From pg_objs as po1, 
		pgs 
	Where pgs.pg_obj_id = po1.id and 
		(Select if(count(po2.id) = 0, true, false) 
		From pg_objs as po2, 
			pg_pg_obj_brg as ppob 
		Where po2.pg_obj_type_id != 14 and 
			ppob.act_bit and 
			po2.id = ppob.pg_obj_id and 
			ppob.pg_id = pgs.id) 
	Order By po1.obj_name";

	return col_pattern($blank_pg_sql, null, null, array('blank_pg_ids', 'blank_pg_names'), $flatten);
	}

//____________________________________________________________________________________
function update_pg_ctx_id($pg_id=null, $pg_ctx_id=null)
{
	if (!check_index($pg_id) ) {return null;}
	if (!check_index($pg_ctx_id) ) {return null;}

	$query = "Update pgs 
	Set pg_ctx_id = ? 
	Where id = ?";

	return ins_pattern($query, 'ii', array('pg_ctx_id'=>$pg_ctx_id, 'pg'=>$pg_id) );
	}

//____________________________________________________________________________________
function get_pg_id_from_pg_pg_obj_brg_id($id=null)
{
	// Get the page id from the pg_pg_obj_brg table where the pg_id is in the pgs table
	if (!check_index($id) ) {return null;}
	
	$query = "Select ppob.pg_id 
		From pg_pg_obj_brg as ppob, 
			pgs
		Where ppob.id = ? and 
			ppob.pg_id = pgs.id and 
			ppob.act_bit 
		Limit 1";
		
	return row_pattern($query, 'i', array($id), array('pg_id') );
	}

//____________________________________________________________________________________


//____________________________________________________________________________________


?>
