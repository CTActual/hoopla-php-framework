<?php
/*
Copyright 2011-2022 Cargotrader, Inc. All rights reserved.

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

// This class gets the value of the object for a page, context, etc.

class hoopla_get_obj_val
{
	public $set_val=null;
	public $output=null;
	public $error=null;
	public $pagenum=null;
	public $type=null;
	public $location=null;
	public $context=null;
	public $input=array();

	function __construct($pagenum=null, $location=null, $ctx=1, $def=false, $type=null, $named_ctx=null, $named_type=null)
	{
		// We require that a page number or URL tag be passed.  If the page URL has no pg GET variable or no POST pg information submitted then 
		// the default value must be handled by the template, if any value exists.  Objects also have the use_def_bit (default=true) setting for each page.

		// If a URL tag is passed as the $pagenum then we will search for the page id, but we cannot guarantee that it will find a unique page.

		// We expect $pagenum (or page name url tag) and location to be filled out since we are putting values onto pages.
		// The type id for the setting can be blank if there is only a value of one type.
		// The context id would default to 1 (the default context), but can be overridden by the named context.
		// The named setting type can be blank as optional or override the type id (whether null or not).
		// Normally, you would be looking at the specific page value, but you can request the default value with $def=true.
		// The use_def_bit will override the default value if the page value is specified, but not the request for a default value.
		// Don't attempt to get the default value by not passing the page id or tag.
		if (empty($pagenum) || empty($location) || !hfw_check_index($ctx) || (!is_numeric($type) && $type !== null) ) 
		{
			$this->error = "<br>hoopla value error<br>"; 
			$this->output = $this->error; 
			return null;
			}

		// First we check to see if pagenum is actually a potential URL/Location tag instead of an id
		if (!is_numeric($pagenum) )
			{$pagenum = get_hfw_pgnum_from_url_tag($pagenum);}

		// Fix any issues with location
		$location = $this->str_fix($location);

		// Use the named context to override the context id (default=1)
		if (!empty($named_ctx) ) {$ctx_id = get_hfw_ctx_id_from_lbl($named_ctx);} else {$ctx_id = $ctx;}

		if (!hfw_check_index($ctx_id) ) {$ctx_id = $ctx;}

		// Force the default lookup to be false if not true
		$def = force_boolean($def, false);

		// Override the type id with the named type lookup.
		if (!empty($named_type) ) {$type_id = get_hfw_type_id_from_lbl($named_type);} else {$type_id = null;}

		if (!hfw_check_index($type_id) ) {$type_id = (hfw_check_index($type) ) ? $type : null;}

		$this->pagenum = $pagenum;
		$this->type = $type_id;
		$this->location = $location;
		$this->context = $ctx_id;

		$extra = "";
		$instr = 'isiii';
		$input = array('pg1'=>$pagenum, 'loc'=>$location, 'ctx'=>$ctx_id, 'def'=>$def, 'pg2'=>$pagenum);

		if (hfw_check_index($type_id) ) 
		{
			$extra = "set_val.pg_obj_set_type_id = ? and ";
			$instr .= 'i';
			$input['type'] = $type_id;
			}

		$query = "Select set_val.pg_obj_set_val, 
			set_val.pg_obj_set_type_id, 
			set_val.pg_id 
		From pg_obj_pg_obj_set_val_brg as set_val, 
			pg_pg_obj_brg as brg, 
			types, 
			ctx, 
			pg_objs 
		Where brg.pg_id = ? and 
			brg.pg_obj_loc = ? and 
			brg.pg_obj_id = set_val.pg_obj_id and 
			set_val.ctx_id = ? and 
			IF(?, IF(ISNULL(set_val.pg_id), true, false), IF( (ISNULL(set_val.pg_id) AND brg.use_def_bit) OR set_val.pg_id = ?, true, false) ) and 
			$extra 
			set_val.act_bit and 
			brg.act_bit and 
			types.id = set_val.pg_obj_set_type_id and 
			types.act_bit and 
			ctx.id = set_val.ctx_id and 
			ctx.act_bit and 
			brg.pg_obj_id = pg_objs.id and 
			pg_objs.act_bit 
		Order by set_val.pg_id DESC";

		$output = hfw_tcol_pattern($query, $instr, $input, array('val', 'type', 'pg') );

		$this->input = $input;
		$this->output = $output;

		return null;
		}	# End of contruct function

	private function str_fix($str=null, $max=255)
	{
		if (!hfw_check_index($max) || $max > 255) {$max = 255;}

		return hfw_mb_prepstr2($str, $max);
		}	# End of function str_fix

	}	# End of hoopla_get_obj_val class

//_____________________________________________________________________________________________________
function hfw_return_value($pagenum=null, $location=null, $ctx=1, $def=false, $type=null, $named_ctx=null, $named_type=null)
{
	// This function will hide some complexity in retrieving values and allow
	// them to be gotten in a single line.
	// We also block some potential errors

	// We expect $pagenum (or page name url tag) and location to be filled out since we are putting values onto pages.
	// The type id for the setting can be blank if there is only a value of one type.
	// The context id would default to 1 (the default context), but can be overridden by the named context.
	// The named setting type can be blank as optional or override the type id (whether null or not).
	// Normally, you would be looking at the specific page value, but you can request ony default values with $def=true.
	// The page object use_def_bit (default=true) setting will block default values (if false) if there are page specific ones unless you set $def=true.
	if (!hfw_check_index($ctx) ) {$ctx = 1;}

	if (empty($pagenum) || empty($location) ) {return null;}

	$def = force_boolean($def, false);

	$set_val_obj = new hoopla_get_obj_val($pagenum, $location, $ctx, $def, $type, $named_ctx, $named_type);

	$set = $set_val_obj->output;

	unset($set_val_obj);

	// We have several possible outcomes from all of this
	# If there is only one output value then that is returned.
	if (is_array($set) && count($set) == 1) 
	{
		$output = (isset($set[0]['val']) && !empty($set[0]['val']) ) ? $set[0]['val'] : null;
		}
	elseif (is_array($set) && count($set) > 1) 
	{
		# We need to know if there is more than setting type or default value-explicit value mix or both.
		$tset = hfw_transpose_output($set);
		
		$pgs = (is_array($tset['pg']) ) ? count(array_unique($tset['pg'], SORT_REGULAR) ) : 0;
		$types = (is_array($tset['type']) ) ? count(array_unique($tset['type'], SORT_REGULAR) ) : 0;

		# If there is more than one setting type id then we goofed in assuming there was only one value.
		# Thus, we need to return the entire result set
		if ($types > 1) {$output = $set;}
		elseif ($pgs > 1 && $types == 1)
		{
			# Here we probably have default-explicit pair ($types == 1 by definition). 
			# We can check this right away, but the default will be the last value, so we take the first.
			$ret = current(array_filter($tset['val'], 'strlen') );

			$output = ($ret !== false) ? $ret : null;
			}
		else {$output = null;}
		}
	elseif (is_array($set) && count($set) == 0) {$output = null;}
	else
	{
		# This must be some text value
		$output = $set;
		}

	return $output;
	}	# End of hfw_return_value function

/*
	hfw_return_value HELP

	The Global first class variable name is $hfwrv.

	Remember that global variables are not automatically accessible within objects, functions, etc. and outside of their original namespace.
	However, they are easily accessible within strings, though {} will be required for function calls.

	INPUT: ($pagenum=null, $location=null, $ctx=1, $def=false, $type=null, $named_ctx=null, $named_type=null)
		$pagenum		required (either the page id itself--an integer--or the page name alias--as text)
		$location		required (always the location text--an alias for the object to be called)
		$ctx				not required, default value=1 (this is always the context id integer, see $named_ctx)
		$def				not required, default value=false (this is either true or false whether to use the default value or not)
		$type				not required, default value=null (this is always the setting type id integer, see $named_type, but only use if there is more than one setting type for an object)
		$named_ctx	not required, default value=null (this is the text alias name for a context, though the default context is assumed so only use if another context is needed)
		$named_type	not required, default value=null (this is the text setting type name alias to be used instead of the id integer--see $type--and only if more than one setting type exists for an object)

	Typical INPUT example: $hfwrv('index', 'loc1')
	Calling a named context: $hfwrv('index', 'loc1', null, null, null, 'other')
	Calling a default value: $hfwrv('index', 'loc1', null, true)

	OUTPUT: Returns the string value of the object in situ, an error message, no value as null, or multiple values in an array if not enough information was given.
		If an array is returned you will likely get an error in your template PHP unless you put in handling for it, but more than likely fixing the call will be better.
		The output array is array[0 ... N]['val'=>the in situ value, 'type'=>the setting type id, 'pg'=>the page id]
		Only one context can be returned since a context is required, even if only the default context.
		The page value generally overrides the default value if it exists.
		While text values are easier to deal with on a template, there is no checking to see if they make any sense.  If not, no value is returned.
*/
//_____________________________________________________________________________________________________
function hfwn_return_value($pagenum=null, $location=null, $named_ctx=null, $named_type=null, $def=false)
{
	// This is a modification of hfw_return value in that only explicitly named search is allowed
	return hfw_return_value($pagenum, $location, null, $def, null, $named_ctx, $named_type);
	}	# End of hfwn_return_value function

/*
	hfwn_return_value HELP

	The Global first class variable name is $hfwnrv--note the "n" here.

	Remember that global variables are not automatically accessible within objects, functions, etc. and outside of their original namespace.
	However, they are easily accessible within strings, though {} will be required for function calls.

	This function is a variation on $hfwrv above, with a different order on INPUT to help with using text value inputs.

	INPUT: ($pagenum=null, $location=null, $named_ctx=null, $named_type=null, $def=false)
		$pagenum	required (either the page id itself--an integer--or the page name alias--as text)
		$location		required (always the location text--an alias for the object to be called)
		$named_ctx	not required, default value=null (this is the text alias name for a context, though the default context is assumed so only use if another context is needed)
		$named_type	not required, default value=null (this is the text setting type name alias, use only if more than one setting type exists for an object)
		$def				not required, default value=false (this is either true or false whether to use the default value or not)

	Typical INPUT example: $hfwnrv('index', 'loc1')
	Calling a named context: $hfwnrv('index', 'loc1', 'other')
	Calling a default value: $hfwnrv('index', 'loc1', null, null, true)

	OUTPUT: See the OUTPUT for $hfwrv above
*/
//_____________________________________________________________________________________________________
function hfwn_csv_return_value($csv=null, $alt=',', $new_set_type=null)
{
	// This function parses a csv string and sends the parameters to hfwn_return_value
	// See help for hfwn_return_value
	// The inputs for hfwnrv are ($pagenum=null, $location=null, $named_ctx=null, $named_type=null, $def=false)
	// One can swap out setting types to get a 'hidden' value under the original.
	if (empty($alt) ) {$alt = ',';}
	
	$params = (isset($csv) && !empty($csv) ) ? explode($alt, $csv) : null;

	if (isset($params) && !empty($params) && is_array($params) && count($params) > 0)
	{
		$finish = count($params);
		
		// There are a maximum of 5 params, so we need to make sure 5 exist
		for ($i=$finish;$i<5;$i++)
		{
			$params[$i] = null;
			}
			
		// Swap out a setting type if necessary
		$params[3] = (!empty($new_set_type) ) ? $new_set_type : $params[3];

		return hfwn_return_value($params[0], $params[1], $params[2], $params[3], $params[4]);
		}	# End of params check
	return null;
	}	# End of hfwn_csv_return_value function
	
//_____________________________________________________________________________________________________
class hoopla_get_all_pg_vals
{
	public $output=null;
	public $error=null;
	public $type=null;
	public $location=null;
	public $context=null;
	public $input=array();

	function __construct($location=null, $ctx=1, $type=null, $named_ctx=null, $named_type=null)
	{
		// This gets all the main values (no default values) of an object from every page to which it is assigned and has a value.
		// This is very useful when you want to get tie together all pages by an object, such as menus and links.

		// The type id for the setting can be blank if there is only a value of one type.
		// The context id would default to 1 (the default context), but can be overridden by the named context.
		// The named setting type can be blank as optional or override the type id (whether null or not).
		// Values are returned in the special order assigned to pages on the Values-by-Object page.

		if (empty($location) || !hfw_check_index($ctx) || (!is_numeric($type) && $type !== null) ) 
		{
			$this->error = "<br>hoopla value error<br>"; 
			$this->output = $this->error; 
			return null;
			}

		// Fix any issues with location
		$location = $this->str_fix($location);

		// Use the named context to override the context id (default=1)
		if (!empty($named_ctx) ) {$ctx_id = get_hfw_ctx_id_from_lbl($named_ctx);} else {$ctx_id = $ctx;}

		if (!hfw_check_index($ctx_id) ) {$ctx_id = $ctx;}

		// Override the type id with the named type lookup.
		if (!empty($named_type) ) {$type_id = get_hfw_type_id_from_lbl($named_type);} else {$type_id = null;}

		if (!hfw_check_index($type_id) ) {$type_id = (hfw_check_index($type) ) ? $type : null;}

		$this->type = $type_id;
		$this->location = $location;
		$this->context = $ctx_id;

		$extra = "";
		$instr = 'si';
		$input = array('loc'=>$location, 'ctx'=>$ctx_id);

		if (hfw_check_index($type_id) ) 
		{
			$extra = "set_val.pg_obj_set_type_id = ? and ";
			$instr .= 'i';
			$input['type'] = $type_id;
			}

		$query = "Select set_val.pg_obj_set_val, 
			set_val.pg_obj_set_type_id, 
			set_val.pg_id, 
			types.type_name, 
			types.std_type_lbl, 
			po1.obj_name as obj_name, 
			po1.acs_str as obj_acs_str, 
			pg_as_obj.obj_name as pg_name, 
			pg_as_obj.id, 
			pg_as_obj.acs_str as pg_acs_str, 
			(Select spc_ord From pg_pg_obj_brg Where pg_id = set_val.pg_id and pg_obj_id = pg_as_obj.id and act_bit Limit 1) as spc_ord, 
			(Select pg_obj_loc From pg_pg_obj_brg Where pg_id = set_val.pg_id and pg_obj_id = pg_as_obj.id and act_bit Limit 1) as loc 
		From pg_obj_pg_obj_set_val_brg as set_val, 
			pg_pg_obj_brg as brg1, 
			types, 
			ctx, 
			pg_objs as po1, 
			pg_objs as pg_as_obj, 
			pgs 
		Where brg1.pg_obj_loc = ? and 
			brg1.pg_obj_id = set_val.pg_obj_id and 
			set_val.ctx_id = ? and 
			set_val.pg_id = brg1.pg_id and 
			$extra 
			set_val.act_bit and 
			brg1.act_bit and 
			types.id = set_val.pg_obj_set_type_id and 
			types.act_bit and 
			ctx.id = set_val.ctx_id and 
			ctx.act_bit and 
			brg1.pg_obj_id = po1.id and 
			po1.act_bit and 
			pgs.id = brg1.pg_id and 
			pg_as_obj.id = pgs.pg_obj_id and 
			pg_as_obj.act_bit 			
		Order by set_val.pg_obj_set_type_id, 
			spc_ord, 
			pg_as_obj.obj_name";

		$output = hfw_tcol_pattern($query, $instr, $input, array('val', 'set_type_id', 'pg_id', 'set_type_name', 'set_type_lbl', 'obj_name', 'obj_acs_str', 'pg_name', 'pg_obj_id', 'pg_acs_str', 'spc_ord', 'loc') );

		$this->output = $output;
		$this->input = $input;

		return null;
		}	# End of contruct function

	private function str_fix($str=null, $max=255)
	{
		if (!hfw_check_index($max) || $max > 255) {$max = 255;}

		return hfw_mb_prepstr2($str, $max);
		}	# End of function str_fix

	}	# End of hoopla_get_all_pg_vals

//_____________________________________________________________________________________________________
function hfw_return_all_vals($location=null, $ctx=1, $type=null, $named_ctx=null, $named_type=null)
{
	// This gets all the main values (no default values) of an object from every page to which it is assigned and has a value.
	// This is very useful when you want to get tie together all pages by an object, such as menus and links.

	// The type id for the setting can be blank if there is only a value of one type.
	// The context id would default to 1 (the default context), but can be overridden by the named context.
	// The named setting type can be blank as optional or override the type id (whether null or not).
	// Values are returned in the special order assigned to pages on the Values-by-Object page.

	if (!hfw_check_index($ctx) ) {$ctx = 1;}

	if (empty($location) || (!hfw_check_index($type) && $type !== null) ) 
		{return null;}

	$set_val_obj = new hoopla_get_all_pg_vals($location, $ctx, $type, $named_ctx, $named_type);

	$set = $set_val_obj->output;

	unset($set_val_obj);

	return $set;
	}	# End of function hfw_return_all_vals
/*
	hfw_return_all_vals HELP

	The Global first class variable name is $hfwrav

	Remember that global variables are not automatically accessible within objects, functions, etc. and outside of their original namespace.
	However, they are easily accessible within strings, though {} will be required for function calls.

	This function is very similar to $hfwrv and $hfwnrv above, but is page independent and does not check default values.

	INPUT: ($location=null, $ctx=1, $type=null, $named_ctx=null, $named_type=null)
		$location		required (always the location text--an alias for the object to be called)
		$ctx				not required, default value=1 (this is always the context id integer, see $named_ctx)
		$type				not required, default value=null (this is always the setting type id integer, see $named_type, but only use if there is more than one setting type for an object)
		$named_ctx	not required, default value=null (this is the text alias name for a context, though the default context is assumed, so only use if another context is needed)
		$named_type	not required, default value=null (this is the text setting type name alias to be used instead of the id integer--see $type--and only if more than one setting type exists for an object)

	Typical INPUT example: $hfwrav('loc1')
	Calling a named context: $hfwrav('loc1', null, null, 'other')
	Forcing a type: $hfwrav('loc1', null, 28)

	OUTPUT: The output array is array[0 ... N]['val'=>the in situ value, 'set_type_id'=>the setting type id, 'obj_name'=>the object name, 'obj_acs_str'=>the object access string, 'pg_id'=>the page name, 'set_type_name'=>the setting type name, 'set_type_lbl'=>the setting type lable, 'pg_name'=>the page name, 'pg_obj_id'=>the page as object id, 'pg_acs_str'=>the page access string, 'spc_ord'=>the special order from Values-by-Object page settings, 'loc'=>the page object location if any of the page as an object (aka page URL)]
		Only one context can be returned since a context is required, even if only the default context.
		Default values are not returned.  Only pages with values are returned.
		More than one set of pages can be returned if there are multiple setting types in use, but the return set provides the setting type information for more filtering.
		The returned set is ordered first by setting type id, then by special order, then by page name.  If the special ordering is not set, then the page name will take over.
		For example, the set of pages with 'HTML' setting types will come first, then the set of pages with 'Text' setting types, etc.  Only if there are values, of course.
*/
//_____________________________________________________________________________________________________
function hfwn_return_all_vals($location=null, $named_ctx=null, $named_type=null)
{
	// This gets all the main values (no default values) of an object from every page to which it is assigned and has a value.
	// This is very useful when you want to get tie together all pages by an object, such as menus and links.
	// This is a helper function for the very similar hfw_return_all_vals--note the 'n' in the name.

	// The named context $named_ctx is optional, making use of the default value of 'default' as needed.
	// The named setting type can be blank .
	// Values are returned in the special order assigned to pages on the Values-by-Object page.

	return hfw_return_all_vals($location, null, null, $named_ctx, $named_type);
	}	# End of function hfw_return_all_vals
/*
	hfwn_return_all_vals HELP

	The Global first class variable name is $hfwnrav--note the "n" here.

	Remember that global variables are not automatically accessible within objects, functions, etc. and outside of their original namespace.
	However, they are easily accessible within strings, though {} will be required for function calls.

	This function is very similar to $hfwrv and $hfwnrv above, but is page independent and does not check default values.

	INPUT: ($location=null, $named_ctx=null, $named_type=null)
		$location		required (always the location text--an alias for the object to be called)
		$named_ctx	not required, default value=null (this is the text alias name for a context, though the default context is assumed, so only use if another context is needed)
		$named_type	not required, default value=null (this is the text setting type name alias, but only if more than one setting type exists for an object)

	Typical INPUT example: $hfwnrav('loc1')
	Calling a named context: $hfwnrav('loc1', 'other')
	Forcing a type: $hfwnrav('loc1', null, 'txt')

	OUTPUT: The output array is array[0 ... N]['val'=>the in situ value, 'set_type_id'=>the setting type id, 'obj_name'=>the object name, 'obj_acs_str'=>the object access string, 'pg_id'=>the page name, 'set_type_name'=>the setting type name, 'set_type_lbl'=>the setting type lable, 'pg_name'=>the page name, 'pg_obj_id'=>the page as object id, 'pg_acs_str'=>the page access string, 'spc_ord'=>the special order from Values-by-Object page settings]
		Only one context can be returned since a context is required, even if only the default context.
		Default values are not returned.  Only pages with values are returned.
		More than one set of pages can be returned if there are multiple setting types in use, but the return set provides the setting type information for more filtering.
		The returned set is ordered first by setting type id, then by special order, then by page name.  If the special ordering is not set, then the page name will take over.
		For example, the set of pages with 'HTML' setting types will come first, then the set of pages with 'Text' setting types, etc.  Only if there are values, of course.
*/
//_____________________________________________________________________________________________________
class hoopla_get_ctx_vals
{
	public $output=null;

	function __construct($ctx=1, $obj_type=null, $set_type=null, $asc_pg=null, $val_pg=null, $named_ctx=null, $named_obj_type=null, $named_set_type=null, $named_asc_pg=null, $named_val_pg=null, $get_def_bit=true)
	{
		// This class retrieves values vertically based on context, with various filters.
		// This class ignores null values and will pull in defaults automatically.
		// A ctx is required, though it might be named instead.
		// The user has the option of sending a ctx or named_ctx array
		// The named_ctx still takes precedence over any ctx values
		
		if (!empty($named_ctx) && !is_array($named_ctx) && !is_array($ctx) )
		{
			$ctx_id = get_hfw_ctx_id_from_lbl($named_ctx);
			$ctx_id = (hfw_check_index($ctx_id) ) ? $ctx_id : ( (hfw_check_index($ctx) ) ? $ctx : 1);
			}
		elseif (!empty($named_ctx) && !is_array($named_ctx) && is_array($ctx) )
		{
			$ctx_id = get_hfw_ctx_id_from_lbl($named_ctx);
			$ctx_id = (hfw_check_index($ctx_id) ) ? $ctx_id : 1;
			}
		elseif (!empty($named_ctx) && is_array($named_ctx) && count($named_ctx) > 0)
		{
			$ctxs = array();
			
			foreach ($named_ctx as $k=>$nc)
			{
				$t = get_hfw_ctx_id_from_lbl($nc);
				$ctxs[] = (hfw_check_index($t) ) ? $t : 1;
				}
			$ctx_id = (count($ctxs) > 0) ? $ctxs : $ctx;
			}
		elseif (empty($named_ctx) && !empty($ctx) && is_array($ctx) && count($ctx) > 0)
		{
			$ctxs = array();
			
			foreach ($ctx as $k=>$nc)
			{
				$t = get_hfw_ctx_id_from_lbl($nc);
				$ctxs[] = (hfw_check_index($t) ) ? $t : 1;
				}
			$ctx_id = (count($ctxs) > 0) ? $ctxs : 1;
			}
		elseif (empty($named_ctx) && !empty($ctx) && !is_array($ctx) )
		{
			$ctx_id = (hfw_check_index($ctx) ) ? $ctx : 1;
			}
			
		// The object type is not required, but can be used as a filter
		if (!empty($named_obj_type) )
			{$obj_type_id = get_hfw_type_id_from_lbl($named_obj_type);}
		
		$obj_type_id = (hfw_check_index($obj_type_id) ) ? $obj_type_id : ( (hfw_check_index($obj_type) ) ? $obj_type : null);
		
		// The setting type is not required, but can be used as a filter
		if (!empty($named_set_type) )
			{$set_type_id = get_hfw_type_id_from_lbl($named_set_type);}
		
		$set_type_id = (isset($set_type_id) && hfw_check_index($set_type_id) ) ? $set_type_id : ( (hfw_check_index($set_type) ) ? $set_type : null);
		
		// One can filter to see if the objects are associated with a specific page
		// This is the page location tag, not the actual page name, which isn't used much.
		if (!empty($named_asc_pg) )
			{$asc_pg_id = get_hfw_pgnum_from_url_tag($named_asc_pg);}
			
		$asc_pg_id = (isset($asc_pg_id) && hfw_check_index($asc_pg_id) ) ? $asc_pg_id : ( (hfw_check_index($asc_pg) ) ? $asc_pg : null);
		
		// One can filter to see if the values are associated with a specific page
		if (!empty($named_val_pg) )
			{$val_pg_id = get_hfw_pgnum_from_url_tag($named_val_pg);}
		else
			{$val_pg_id = null;}
			
		$val_pg_id = (hfw_check_index($val_pg_id) ) ? $val_pg_id : ( (hfw_check_index($val_pg) ) ? $val_pg : null);
		
		// $get_def_bit or get default value bit will retrieve default values if true (the default for the bit), or not if false
		$get_def_bit = force_boolean($get_def_bit, true);
		
		// We start with the default inputs
		$ctx_row = "p.ctx_id = ? and ";
		$i = '';
		$input = array();
		
		// We need to deal with the fact $ctx_id might be an array
		if (is_array($ctx_id) && count($ctx_id) > 0)
		{
			$ctx_row = "p.ctx_id IN (";
			
			// We can assume all $ctx are numerical because they passed check_index
			foreach ($ctx_id as $k=>$c)
			{
				$ctx_row .= "{$c}, ";
				}
			$ctx_row = substr($ctx_row, 0, -2) . ") and ";
			}
		else
		{			
			$i = 'i';
			$input = array('ctx'=>$ctx_id);
			}
			
		$obj_type_extra = "";
		$set_type_extra = "";
		$asc_pg_extra = "";
		$val_pg_extra = "";
		
		// We go through with adding additional filters
		if (hfw_check_index($obj_type_id) )
		{
			$i .= 'i';
			$input['obj_type_id'] = $obj_type_id;
			$obj_type_extra = "po.pg_obj_type_id = ? and ";
			}
			
		if (hfw_check_index($set_type_id) )
		{
			$i .= 'i';
			$input['set_type_id'] = $set_type_id;
			$set_type_extra = "set_type.id = ? and ";
			}
			
		if (hfw_check_index($asc_pg_id) )
		{
			$i .= 'i';
			$input['asc_pg_id'] = $asc_pg_id;
			$asc_pg_extra = "ppob.pg_id = ? and ";
			}
			
		if (hfw_check_index($val_pg_id) )
		{
			$i .= 'i';
			$input['val_pg_id'] = $val_pg_id;
	
			// We make use of the use_def_bit to fine-tune the results worry-free
			if ($get_def_bit)
				{$val_pg_extra = "(p.pg_id = ? or (p.pg_id Is NULL and ppob.use_def_bit) ) and ";}
			else
				{$val_pg_extra = "p.pg_id = ? and ";}
			}
			
		// Compile the query
		$query = "Select p.id, 
			p.pg_obj_id, 
			p.pg_obj_set_type_id, 
			p.pg_obj_set_val, 
			p.pg_id, 
			p.ctx_id, 
			ctx.ctx_name, 
			ctx.ctx_lbl, 
			set_type.type_name, 
			set_type.std_type_lbl, 
			obj_type.type_name, 
			obj_type.std_type_lbl, 
			po.obj_name, 
			ppob.pg_id, 
			ppob.pg_obj_loc, 
			ppob.spc_ord, 
			ppob.use_def_bit 
		From pg_obj_pg_obj_set_val_brg as p, 
			pg_pg_obj_brg as ppob, 
			types as set_type, 
			pg_objs as po, 
			types as obj_type, 
			ctx 
		Where p.act_bit and 
			$ctx_row 
			p.pg_obj_set_val Is Not NULL and 
			set_type.id = p.pg_obj_set_type_id and 
			p.pg_obj_id = ppob.pg_obj_id and 
			p.ctx_id = ctx.id and 
			ppob.act_bit and 
			po.id = p.pg_obj_id and 
			po.act_bit and 
			$obj_type_extra 
			$set_type_extra 
			$asc_pg_extra 
			$val_pg_extra 
			obj_type.id = po.pg_obj_type_id 
		Order By ppob.spc_ord, 
			po.obj_name, 
			p.pg_id DESC";
			
		$output = array('set_val_id', 'pg_obj_id', 'pg_obj_set_type_id', 'val', 'set_val_pg_id', 'ctx_id', 'ctx_name', 'ctx_lbl', 
								'set_type_name', 'set_type_lbl', 'obj_type_name', 'obj_type_lbl', 'obj_name', 'asc_pg_id', 
								'asc_pg_obj_loc', 'obj_spec_ord', 'use_def_bit');

		$this->output = hfw_tcol_pattern($query, $i, $input, $output);
		return null;
		}	# End of contruct function

	}	# End of hoopla_get_ctx_vals class

//_____________________________________________________________________________________________________
function hfw_get_ctx_vals($ctx=1, $obj_type=null, $set_type=null, $asc_pg=null, $val_pg=null, $named_ctx=null, $named_obj_type=null, $named_set_type=null, $named_asc_pg=null,  $named_val_pg=null, $get_def_bit=true)
{
	if (!hfw_check_index($ctx) ) {$ctx = 1;}

	$set_val_obj = new hoopla_get_ctx_vals($ctx, $obj_type, $set_type, $asc_pg, $val_pg, $named_ctx, $named_obj_type, $named_set_type, $named_asc_pg, $named_val_pg, $get_def_bit);

	$set = $set_val_obj->output;

	unset($set_val_obj);

	return $set;
	}	# End of hfw_get_ctx_vals function
/*
	hfw_get_ctx_vals HELP

	The Global first class variable name is $hfwgcv.

	Remember that global variables are not automatically accessible within objects, functions, etc. and outside of their original namespace.
	However, they are easily accessible within strings, though {} will be required for function calls.

	This function retrieves values vertically based on context, with various filters.
	This function ignores null values and will pull in defaults automatically.
	Use the input to filter down output as needed.

	INPUT: ($ctx=1, $obj_type=null, $set_type=null, $asc_pg=null, $named_ctx=null, $named_obj_type=null, $named_set_type=null, $named_asc_pg=null)
		$ctx						required [num or array of num], default value=1 (this is the context id, but can be overruled by the named context)
		$obj_type				not required (the type id of the page object, which can be overruled by the named obj type label)
		$set_type				not required (the type id of the setting, which can be overruled by the named setting type label)
		$asc_pg					not required (the page id of the object associated page, not the setting value page, and can be overruled by the named associated page)
		$val_pg					not required (the page id of the setting value page, and can be overruled by the named value page)
		$named_ctx			not required [string or array of strings], default value=null (this is the text alias/shorthand name for a context, which will override the id entered for $ctx)
		$named_obj_type	not required, default value=null (this is the object type name alias, which will override any entry for $obj_type)
		$named_set_type	not required, default value=null (this is the set type name alias, which will override any entry for $set_type)
		$named_asc_pg		not required (the page tag of the object associated page, not the setting value page, which will override any entry to $asc_pg)
		$named_val_pg		not required (the page tag of the setting value page, which will override any entry to $val_pg)
		$get_def_bit			required, default value=true (retreive default values if true, or only page values if false, if the setting value page has been set)

	Typical INPUT example, calling a context, object type and setting type by name: $hfwgcv(null, null, null, null, 'ctx1', 'frm_obj', 'html')
	The same using ids: $hfwgcv(2, 8, 19)

	OUTPUT: The output array is array[0 ... N]['set_val_id'=>the setting id, 'pg_obj_id'=>the page object id, 'pg_obj_set_type_id'=>the page object setting type id, 'val'=>the setting value, 'set_val_pg_id'=>the page id for the setting value-if any, 'ctx_id'=>context id, 'ctx_name'=>context name, 'ctx_lbl'=>context label, 'set_type_name'=>the type name, 'set_type_lbl'=>the setting type label, 'obj_type_name'=>the page object type name, 'obj_type_lbl'=>the page object type label, 'obj_name'=>the object label, 'asc_pg_id'=>the id of the object associated page, 'asc_pg_obj_loc'=>the label tag of the object associated page]
		A minimum of one context can be returned since a context is required, even if only the default context.
		Default values are returned if the obj use default bit is set.   These will need to be handled in code if there are page specific values as well.
		If no page is used as an object filter, then all the objects of a given context, object type (if any) and setting type (if any) will be returned.
		If no object type is used as an object filter then objects of all types will be returned.
		If no setting type is used as a filter, then setting values of all types will be returned.
		The returned set is ordered first by the object special order, then by object name.  If the special ordering is not set, then the object name will take over.
*/
	
//_____________________________________________________________________________________________________
function hfwn_get_ctx_vals($named_ctx=null, $named_obj_type=null, $named_set_type=null, $named_asc_pg=null, $named_val_pg=null, $get_def_bit=true)
{
	$set_val_obj = new hoopla_get_ctx_vals(null, null, null, null, null, $named_ctx, $named_obj_type, $named_set_type, $named_asc_pg, $named_val_pg, $get_def_bit);

	$set = $set_val_obj->output;

	unset($set_val_obj);

	return $set;
	}	# End of hfwn_get_ctx_vals function
/*
	hfwn_get_ctx_vals HELP

	The Global first class variable name is $hfwngcv--note the "n" here.

	Remember that global variables are not automatically accessible within objects, functions, etc. and outside of their original namespace.
	However, they are easily accessible within strings, though {} will be required for function calls.

	This function retrieves values vertically based on context, with various filters.
	This function ignores null values and will pull in defaults automatically.
	Use the input to filter down output as needed.

	INPUT: ($named_ctx=null, $named_obj_type=null, $named_set_type=null, $named_asc_pg=null)
		$named_ctx			not required [string or array of strings], default value=null (this is the text alias/shorthand name for a context.  If no value is entered then the default context is assumed)
		$named_obj_type	not required, default value=null (this is the object type name alias)
		$named_set_type	not required, default value=null (this is the set type name alias)
		$named_asc_pg		not required (the page tag of the object associated page, not the setting value page)
		$named_val_pg		not required (the page tag of the setting value page)
		$get_def_bit			required, default value=true (retreive default values if true, or only page values if false, if the setting value page has been set)

	Typical INPUT example, calling a context, object type and setting type by name: $hfwngcv('ctx1', 'frm_obj', 'html')
	Getting the default context values for objects associated with the given page: $hfwngcv(null, null, null, 'pg')

	OUTPUT: The output array is array[0 ... N]['set_val_id'=>the setting id, 'pg_obj_id'=>the page object id, 'pg_obj_set_type_id'=>the page object setting type id, 'val'=>the setting value, 'set_val_pg_id'=>the page id for the setting value-if any, 'ctx_id'=>context id, 'ctx_name'=>context name, 'ctx_lbl'=>context label, 'set_type_name'=>the type name, 'set_type_lbl'=>the setting type label, 'obj_type_name'=>the page object type name, 'obj_type_lbl'=>the page object type label, 'obj_name'=>the object label, 'asc_pg_id'=>the id of the object associated page, 'asc_pg_obj_loc'=>the label tag of the object associated page]
		A minimum of one context can be returned since a context is required, even if only the default context.
		Default values are returned if the obj use default bit is set.   These will need to be handled in code if there are page specific values as well.
		If no page is used as an object filter, then all the objects of a given context, object type (if any) and setting type (if any) will be returned.
		If no object type is used as an object filter then objects of all types will be returned.
		If no setting type is used as a filter, then setting values of all types will be returned.
		The returned set is ordered first by the object special order, then by object name.  If the special ordering is not set, then the object name will take over.
*/

//_____________________________________________________________________________________________________
function hfw_get_pg_list($pg_ctx=null)
{
	// Filter by page context
	if (!empty($pg_ctx) )
	{
		$str = 's';
		$input = array('pg_ctx'=>hfw_mb_prepstr($pg_ctx) );
		$extra = "and ctx.ctx_lbl = ? ";
		}

	$obj_sql = "Select pg_objs.id, 
		obj_name, 
		obj_dsr, 
		ppob.pg_obj_loc, 
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
				pg_id, 
				spc_ord 
			From pg_pg_obj_brg as ppob, 
				pgs, 
				pg_objs 
			Where pg_id = pgs.id and 
				pg_objs.id = ppob.pg_obj_id and 
				pgs.pg_obj_id = pg_objs.id) as ppob On 
			(pg_id = pgs.id) 
	Where pgs.pg_obj_id = pg_objs.id and 
		pgs.pg_ctx_id = ctx.id $extra 
	Order by ppob.spc_ord, 
		obj_name, 
		pg_objs.id";

	$result_array_1 = array(	'obj_id', 
									'obj_name', 
									'obj_dsr', 
									'url_tag', 
									'acs_str', 
									'pg_id', 
									'pg_ctx_id', 
									'pg_ctx_name', 
									'pg_ctx_lbl', 
									'act_bit');

	return hfw_tcol_pattern($obj_sql, $str, $input, $result_array_1);
	}
/*
	hfw_get_pg_list HELP

	The Global first class variable name is $hfwgpl.

	Remember that global variables are not automatically accessible within objects, functions, etc. and outside of their original namespace.
	However, they are easily accessible within strings, though {} will be required for function calls.

	This function is a duplicate of the IDE class get_pg_list, except it uses the label instead of the page context id, and the ordering is by special order.

	INPUT: ($pg_ctx=null)
		$pg_ctx		not required (the string label of the page context id, which may or may not be easy to use, the default page context is set as def_pg_ctx)

	OUTPUT: The output array is array[0 ... N][	'obj_id'=>the page object id, 
		'obj_name'=>the page object name (page name), 
		'obj_dsr'=>the page object description (page description), 
		'url_tag'=>the page object location (the page url tag id), 
		'acs_str'=>the page added security feature if you need one, 
		'pg_id'=>the page id from the pgs table, not the pg_objs table, 
		'pg_ctx_id'=>the page context id--no relation to object value contexts, 
		'pg_ctx_name'=>the page context name, 
		'pg_ctx_lbl'=>the page context label, 
		'act_bit'=>the page state]

	This function is primarily useful for vetting page calls to templates, since the context can match a template.
*/
//_____________________________________________________________________________________________________
function hfw_get_acs_str($obj_id=null)
{
	if (!check_index($obj_id) ) {return null;}

	$query = "Select acs_str 
		From pg_objs 
		Where id = ? 
		Limit 1";

	return row_pattern($query, 'i', array('id'=>$obj_id), array('acs_str') );
	}
/*
	hfw_get_acs_str HELP

	The Global first class variable name is $hfwgas

	Remember that global variables are not automatically accessible within objects, functions, etc. and outside of their original namespace.
	However, they are easily accessible within strings, though {} will be required for function calls.

	This basic function returns the singular value of any access string assigned to an object.	

	INPUT: ($obj_id=null)
		$obj_id		required (the numerical id of the object--gotten from another query in most cases.)

	OUTPUT: acs_str value (string)
*/
//_____________________________________________________________________________________________________
function hfw_get_all_locs($url_tag=null, $named_pg_ctx=null, $named_obj_type=null, $act_obj=true)
{
	// Get the distinct locations of objects on pages, filtered by url_tag, page context, object type and/or activity.
	// An active object (default) must both be actively associated with the page and itself be active in the system.
	// if $act_obj is false, objects either active or inactive will be returned.
	// We don't require a url_tag, ctx or obj_type.  These will filter down results accordingly.
	
	// Input defaults
	$str = '';
	$input = array();
	$url_extra = '';
	
	// Deal with the url tag
	if (!empty($url_tag) ) 
	{
		$pagenum = get_hfw_pgnum_from_url_tag($url_tag);
		$str .= 'i';
		$input[] = $pagenum;
		$url_extra = "ppob.pg_id = ? and ";
		}
		
	// Deal with the page context (not object value context)
	if (!empty($named_pg_ctx) )
	{
		$ctx_id = get_hfw_ctx_id_from_lbl($named_pg_ctx);
		$str .= 'i';
		$input[] = $ctx_id;
		$ctx_extra = "pgs.pg_ctx_id = ? and ";
		}
		
	// Deal with the named object type
	if (!empty($named_obj_type) )
	{
		$type_id = get_hfw_type_id_from_lbl($named_obj_type);
		$str .= 'i';
		$input[] = $type_id;
		$type_extra = "pg_objs.pg_obj_type_id = ? and ";
		}

	// Deal with activity
	if ($act_obj == true)
	{
		$act_extra = "ppob.act_bit and pg_objs.act_bit and ";
		}
		
	$query = "Select DISTINCT pg_obj_loc, 
					pg_objs.id, 
					pg_objs.obj_name, 
					pg_objs.acs_str, 
					pg_objs.act_bit, 
					ppob.act_bit 
				From pg_pg_obj_brg as ppob, 
					pg_objs, 
					pgs 
				Where $url_extra 
					$ctx_extra 
					$type_extra 
					$act_extra 
					ppob.pg_id = pgs.id and 
					ppob.pg_obj_id = pg_objs.id 					
				Order by ppob.spc_ord, 
					pg_obj.obj_name";

	return tcol_pattern($query, $str, $input, array('location', 'obj_id', 'obj_name', 'acs_str', 'obj_act_bit', 'brg_act_bit') );
	}
/*
	hfw_get_all_locs HELP

	The Global first class variable name is $hfwgal

	Remember that global variables are not automatically accessible within objects, functions, etc. and outside of their original namespace.
	However, they are easily accessible within strings, though {} will be required for function calls.

	The function returns the list of distinct locations for a page, and the associated objects.

	INPUT: 	($url_tag=null, $named_pg_ctx=null, $named_obj_type=null, $act_obj=true)
		$url_tag					not required (the "page name" of interest.  This is the same tag used to get values.)
		$named_pg_ctx		not required (the named page context, not a value context.)
		$named_obj_type	not required (the type of object.)
		$act_obj					not required, default value=true (If true only active objects and actively used objects will be returned, otherwise, any)

	OUTPUT: The output array is array[0 ... N]['location'=>location tags on the page, 
		'obj_id'=>the id of the object associated with the location, 
		'obj_name'=>the name of object associated with the location, 
		'acs_str'=>the arbitrary access string assigned to the object, 
		'obj_act_bit'=>true if the object is active, or false, 
		'brg_act_bit'=>true if actively used on a page, otherwise false.].

	This function is good for auto-discovery for a page, particularly if location info needs to be fed into some sort of loop.
	There is no assumption made about any values assigned to the object.
*/
//_____________________________________________________________________________________________________
// Below are the helper functions for the above.  
//_____________________________________________________________________________________________________
function get_hfw_pgnum_from_url_tag($tag=null)
{
	// Instead of requiring the actual page id to be passed, which can be vague and hard to remember in code, 
	// the passed tag can be text.

	if ($tag === NULL) {return null;}

	$tag = hfw_mb_prepstr2($tag, 255);

	$find_id_sql = "Select pg_id 
				From pg_pg_obj_brg as ppob, 
					pgs, 
					pg_objs 
				Where ppob.pg_id = pgs.id and 
					ppob.pg_obj_loc = ? and 
					ppob.pg_obj_id = pg_objs.id and 
					pgs.pg_obj_id = pg_objs.id 
				Limit 1";

	$pagenum = hfw_row_pattern($find_id_sql, 's', array('loc'=>$tag), array('hoopla_returned_page_id') );
	
	if (is_numeric($pagenum) ) {return $pagenum;} else {return null;}
	}	# End of get_fw_pgnum_from_url_tag function
//_____________________________________________________________________________________________________
function get_hfw_ctx_id_from_lbl($ctx=null)
{
	// We can get the context id from the context label, if the id exists.
	if (empty($ctx) ) {return null;}

	$newctx = hfw_mb_prepstr($ctx, 31);

	$query = "Select id 
	From ctx 
	Where ctx_lbl = ? 
	Limit 1";

	$output = hfw_row_pattern($query, 's', array($newctx), array('ctx_id') );
	
	// We need a guard against no html in the string
	if (hfw_check_index($output) ) {return $output;}
	
	return hfw_row_pattern($query, 's', array($ctx), array('ctx_id') );
	}	# End of get_hfw_ctx_id_from_lbl function

//_____________________________________________________________________________________________________
function get_hfw_type_id_from_lbl($type=null)
{
	// We can get the type id from the type label, if the id exists.
	if (empty($type) ) {return null;}

	$newtype = hfw_mb_prepstr($type, 63);

	$query = "Select id 
	From types 
	Where std_type_lbl = ? 
	Limit 1";

	$output = hfw_row_pattern($query, 's', array($newtype), array('type_id') );
	
	// We need a guard against no html in the string
	if (hfw_check_index($output) ) {return $output;}
	
	return hfw_row_pattern($query, 's', array($type), array('type_id') );
	}	# End of get_hfw_ctx_id_from_lbl function

//_____________________________________________________________________________________________________
function hfw_mb_prepstr($input, $length=null)
{
	if (hfw_check_index($length) )
		{return htmlentities(mb_substr(trim($input), 0, $length), ENT_QUOTES | ENT_HTML5, "UTF-8", false);}
	else
		{return htmlentities(trim($input), ENT_QUOTES | ENT_HTML5, "UTF-8", false);}
	}

// ____________________________________________________________________________________________________
function hfw_mb_prepstr2($input, $length=null)
{
	if (hfw_check_index($length) )
		{return htmlspecialchars(mb_substr(trim($input), 0, $length), ENT_QUOTES | ENT_HTML5, "UTF-8", false);}
	else
		{return htmlspecialchars(trim($input), ENT_QUOTES | ENT_HTML5, "UTF-8", false);}
	}

// ____________________________________________________________________________________________________
function hfw_check_index($val=null, $var=true)
{
	if ($var)
	{
		if (!empty($val) && is_numeric($val) && $val > 0) {return true;} else {return false;}
		}
	else
	{
		if (empty($val) || !is_numeric($val) || $val < 1) {return true;} else {return false;}
		}
	}	# End of print out array function

// ____________________________________________________________________________________________________
function hfw_check_indexes($vals=array(), $var=true)
{
	// For simple arrays only, not multi-dimensional ones.
	if (!is_array($vals) ) {return false;}
	if (count($vals) == 0) {return false;}

	// check all values to be true or false ($var)
	foreach ($vals as $val)
	{
		if (!check_index($val, $var) ) {return false;}
		}

	return true;
	}

// ____________________________________________________________________________________________________
function hfw_force_boolean($var, $def=true)
{
	if ($def != true && $def != false) {$def = true;}
	if ($var != true && $var != false) {return $def;} else {return $var;}
	}	# End of print out array function

// ____________________________________________________________________________________________________
// First class variable names for the main functions above.
// ____________________________________________________________________________________________________
$hfwrv = 'hfw_return_value';
$hfwnrv = 'hfwn_return_value';
$hfwrav = 'hfw_return_all_vals';
$hfwnrav = 'hfwn_return_all_vals';
$hfwgcv = 'hfw_get_ctx_vals';
$hfwngcv = 'hfwn_get_ctx_vals';
$ghfwpfut = 'get_hfw_pgnum_from_url_tag';
$ghfwcid = 'get_hfw_ctx_id_from_lbl';
$ghfwtid = 'get_hfw_type_id_from_lbl';
$hfwgpl = 'hfw_get_pg_list';
$hfwgas = 'hfw_get_acs_str';
$hfwgal = 'hfw_get_all_locs';
$csvhfwnrv = 'hfwn_csv_return_value';
?>
