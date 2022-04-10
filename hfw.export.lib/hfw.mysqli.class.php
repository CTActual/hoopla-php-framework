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

// The mysqli object class is an attempt to simplify the process of making prepared statements, though perhaps at the expense of performance

class hfw_mysqli_obj
{
	public $mysqli_con_fo;		# the actual connection object to the database
	public $mysqli_con_bool;	# returns true or false if there is a connection
	public $mysqli_con_err;	# returns string of the error message, if any, for the connection
	public $mysqli_local=false;	# use a class var instead of a global for output

	public $mysqli_qry;		# is the string for the sql query
	private $mysqli_do_store_result_bool;	# stores true or false if a store_result command is needed

	public $mysqli_stmt_fo;	# the actual prepared statement
	public $mysqli_stmt_bool;	# returns true or false if there is a prepared statement
	public $mysqli_stmt_err;	# returns string of the error message, if any, for the prepared connection
	public $mysqli_stmt_param_count;	# returns the number of parameters that are contained in the prepared connection

	public $mysqli_bind_types_str;	# the allowed type string for binding statements
	public $mysqli_bind_array = array();	# the array of variables to bind the statement to
	public $mysqli_bind_stmt_str;		# the string of the binding php statement
	public $mysqli_bind_bool;			# returns true if the stmt was bound
	public $mysqli_bind_err;			# returns the error message, if any, from binding

	public $mysqli_exec_result_bool;	# returns true or false if there is a valid result
	public $mysqli_exec_result_err;		# returns the error string, if any, for the result
	public $mysqli_exec_row_count;	# returns the number of rows affected by the execution of the statement (update/insert)
	public $mysqli_exec_num_rows;		# returns the number of rows in the result stored variable (select)
	public $mysqli_exec_meta_data;		# returns the official meta data of the statement after execution

	public $mysqli_store_result_bool;	# stores true if results were stored

	public $mysqli_bind_result_array = array();	# the array of variables to bind the results to
	public $mysqli_output = array();			# the local output variable
	public $mysqli_col = array();			# the local output variable for fetch columns
	public $mysqli_bind_result_bool;	# stored true if results were bound
	public $mysqli_bind_result_stmt;	# the string for binding the php statement
	public $mysqli_bind_result_err;		# returns the error message, if any, from binding
	public $mysqli_bind_result_allow_bool;	# flag for blocking multi-bind calls
	public $mysqli_fetch_bool;			# returns the boolean for fetching results
	public $mysqli_goto_bool;				# returns the goto boolean
	public $mysqli_insert_id;				# returns the id of the last insert (if any)
	public $mysqli_start_time;				# performance testing
	public $mysqli_report;				# diagnostics results

	// We automatically connect to the database during the construct

	function __construct($user_type=null, $local=false)
	{
		$this->mysqli_start_time = microtime(true);
		$this->mysqli_con_fo = new mysqli();
		$this->mysqli_con_fo->init();
		if ($local) {$this->mysqli_local = true;}

		include ($GLOBALS['extclasspath'] . "hfw.db.info.php");

		$this->mysqli_con_bool = true;

		$this->mysqli_con_fo->real_connect($dsn['hostspec'], $dsn['username'], $dsn['password'], $dsn['database'], $dsn['port'], $dsn['socket']);

		if (mysqli_connect_error() )
		{
			$this->mysqli_con_bool = false;
			$this->mysqli_con_err= $this->mysqli_con_fo->error;
			}

		$this->mysqli_stmt_bool = false;	
		$this->mysqli_do_store_result_bool = true;
		$this->mysqli_bind_bool = false;
		$this->mysqli_exec_result_bool = false;
		$this->mysqli_store_result_bool = false;
		$this->mysqli_stmt_param_count = -1;
		$this->mysqli_bind_result_allow_bool = true;
		$this->mysqli_qry = null;
		$this->mysqli_insert_id = 0;

		return $this->mysqli_con_bool;
		}	# End of __Construct function

	function prep_stmt()
	{
		if ($this->mysqli_con_bool  && $this->mysqli_qry != null && $this->mysqli_qry != "")
		{
			$this->mysqli_stmt_fo = $this->mysqli_con_fo->stmt_init();
			$this->mysqli_stmt_bool = $this->mysqli_stmt_fo->prepare($this->mysqli_qry);
			$this->mysqli_stmt_err = $this->mysqli_stmt_fo->error;
			if ($this->mysqli_stmt_bool)
			{
				$this->mysqli_stmt_param_count = $this->mysqli_stmt_fo->param_count;
				$this->qry_check();
				$this->mysqli_bind_result_allow_bool = true;
				}	# end of inner conditional
			else
			{
				$this->mysqli_con_fo->close();
				}
			}	# End of main conditional
		else
		{
			$this->mysqli_con_fo->close();
			}
		return $this->mysqli_stmt_bool;
		}	# End of prep_stmt function

	function bind_stmt()
	{
		if ($this->mysqli_con_bool && $this->mysqli_stmt_bool)
		{
			$bind_stmt = '$this->mysqli_bind_bool = $this->mysqli_stmt_fo->bind_param($this->mysqli_bind_types_str, ';

			if (is_array($this->mysqli_bind_array) && $this->mysqli_bind_array != null && $this->mysqli_bind_types_str != null && $this->mysqli_bind_types_str != "")
			{
				$param_str = "";

				// Once the binding object is created, values assigned to it are automatically bound as well
				// However, they are bound individually as far as I can tell, so the appearance of an array with an index
				// is somewhat of an illusion, as the values must be assigned one at a time.
				#While (list($arrayindex, $indexvalue) = each($this->mysqli_bind_array))
				foreach ($this->mysqli_bind_array as $arraykey => $bound_value)
				{
					$param_str .= "\$this->mysqli_bind_array['" . $arraykey . "'], ";
					}

				$param_str = substr($param_str, 0, -2) . ');';
				$bind_stmt .= $param_str;

				eval($bind_stmt);
				$this->mysqli_bind_stmt_str = $bind_stmt;
				$this->mysqli_bind_err = $this->mysqli_stmt_fo->error;
				$this->mysqli_bind_result_allow_bool = true;
				}	# End of inner conditional
			else
			{
				$this->mysqli_bind_bool = true;
				}	# End of inner conditional
			}	# End of main conditional
		return $this->mysqli_bind_bool;
		}	# End of bind_stmt function

	function rebind($bind_array=array() )
	{
		if (isset($bind_array) && is_array($bind_array) && count($bind_array) > 0)
		{
			foreach ($bind_array as $key => $value)
			{
				$this->mysqli_bind_array[$key] = $value;
				}
			}
		}	# End of function rebind

	function exec_stmt()
	{
		if ($this->mysqli_con_bool && $this->mysqli_stmt_bool && $this->mysqli_bind_bool)
		{
			$this->mysqli_exec_result_bool = $this->mysqli_stmt_fo->execute();
			$this->mysqli_exec_result_err = $this->mysqli_stmt_fo->error;
			$this->mysqli_exec_meta_data = $this->mysqli_stmt_fo->result_metadata();
			$this->mysqli_exec_row_count = $this->mysqli_stmt_fo->affected_rows;

			if ($this->mysqli_exec_result_bool)
			{
				if ($this->mysqli_do_store_result_bool)
				{
					$this->mysqli_store_result_bool = $this->mysqli_stmt_fo->store_result();

					if ($this->mysqli_store_result_bool) {$this->mysqli_exec_num_rows = $this->mysqli_stmt_fo->num_rows;}
					}
				else
				{
					$this->mysqli_insert_id = $this->mysqli_con_fo->insert_id;
					}
				}	# End of result error checking conditional
			}	# End of main conditional
		return $this->mysqli_exec_result_bool;
		}	# End of exec_stmt function

	function stmt_exec()
	{
		return $this->exec_stmt();
		} # End of stmt_exec function (synonym for exec_stmt)

	function e()
	{
		return $this->exec_stmt();
		} # End of e function (synonym for exec_stmt)

	function bind_result()
	{
		if ($this->mysqli_con_bool && $this->mysqli_stmt_bool && $this->mysqli_bind_bool && $this->mysqli_exec_result_bool && $this->mysqli_store_result_bool && $this->mysqli_bind_result_allow_bool)
		{
			$this->mysqli_output = array();

			$bind_stmt = '$this->mysqli_bind_result_bool = $this->mysqli_stmt_fo->bind_result(';

			if (is_array($this->mysqli_bind_result_array) && $this->mysqli_bind_result_array != null)
			{
				$param_str = "";

				$close_str = "'], ";

				if ($this->mysqli_local)
					{$output_scope_str = "\$this->mysqli_output['";}
				else
					{$output_scope_str = "\$GLOBALS['";}

				foreach ($this->mysqli_bind_result_array as $result_array_var_name)
					{$param_str .= $output_scope_str . $result_array_var_name . $close_str;}

				$param_str = substr($param_str, 0, -2) . ');';
				$bind_stmt .= $param_str;

				eval($bind_stmt);

				$this->mysqli_bind_result_allow_bool = false;
				$this->mysqli_bind_result_stmt = $bind_stmt;
				$this->mysqli_bind_result_err = $this->mysqli_stmt_fo->error;
				if ($this->mysqli_stmt_fo->num_rows == 0) {$this->mysqli_output = array();}
				unset($param_str);
				unset($bind_stmt);
				}	# End of inner conditional
			else
			{
				$this->mysqli_bind_result_bool = false;
				}	# End of inner conditional
			}	# end of main conditional
		return $this->mysqli_bind_result_bool;
		}	# end of result_bind function

	function fetch()
	{
		if (is_object($this->mysqli_stmt_fo) )
		{
			$this->mysqli_fetch_bool = $this->mysqli_stmt_fo->fetch();
			return $this->mysqli_fetch_bool;
			}
		else
			{return false;}
		} # End of fetch function

	function get_row()
	{
		if (is_object($this->mysqli_stmt_fo) )
		{
			$this->mysqli_fetch_bool = $this->mysqli_stmt_fo->fetch();
			return $this->mysqli_fetch_bool;
			}
		else
			{return false;}
		} # End of get_row function (synonym for fetch)

	function fetch_cols($input=array(), $output=array(), $flatten=false, $flatstr="__r__")
	{
		$i = 0;	# Check if there are no results returned
		$cols = array();	# Temp var to hold results

		$this->goto_row();

		// Generally, only when storing variables locally will we fetch them all at once.
		if ($this->mysqli_local)
		{
			while ($this->fetch() )
				{foreach($input as $index=>$var)
					{$cols[$output[$index]][] = $this->mysqli_output[$var];} $i++;}
			}
		else 	// This is an obscure use case, but needed for backwards compatibility
		{
			while ($this->fetch() )
				{foreach($input as $index=>$var)
					{$cols[$output[$index]][] = $GLOBALS[$var];} $i++;}
			}

		// We store everything in $cols to speed things up and compact the code
		if (!$flatten)
			{$this->mysqli_col = $cols;}
		else
		{
			foreach($input as $index=>$var)
			{
				if ($i == 0) 
					{$this->mysqli_col[$output[$index]] = "";}
				else
					{$this->mysqli_col[$output[$index]] = implode($flatstr, $cols[$output[$index]]);}
				}
			}	# End of flatten return values

		unset($cols);

		// This is an obscure use case, but needed for backwards compatibility
		if ($this->mysqli_local == false)
		{
			$GLOBALS = $GLOBALS + $this->mysqli_col;
			$this->mysqli_col = array();
			}

		$this->goto_row();
		return $this->mysqli_fetch_bool;
		}	# End of get_cols function

	function fetch_tcols($output=array() )
	{
		$this->goto_row();

		$col = array();
		$this->mysqli_col = array();

		// This is not backwards compatible with $GLOBALS
		// Nor does it allow separate input/output vars
		if ($this->mysqli_local)
		{
			$row = array();

			while ($this->fetch() )
			{
				foreach ($output as $key=>$ovar)
					{$row[$ovar] = $this->mysqli_output[$ovar];}

				$col[] = $row;
				$row = array();
				}
			}

		$this->mysqli_col = $col;
		$this->goto_row();
		return $this->mysqli_fetch_bool;
		}	# End of get_cols function

	function reset()
	{
		$this->mysqli_stmt_fo->close();

		$this->mysqli_stmt_bool = false;
		$this->mysqli_do_store_result_bool = true;
		$this->mysqli_bind_bool = false;
		$this->mysqli_exec_result_bool = false;
		$this->mysqli_store_result_bool = false;
		$this->mysqli_stmt_param_count = -1;
		$this->mysqli_bind_result_allow_bool = true;
		$this->mysqli_qry = null;
		$this->mysqli_insert_id = 0;

		return $this->mysqli_stmt_bool;
		} # End of reset function

	function auto_init($sql = null, $a_str = null, $b_array = null, $r_array = null)
	{
		$this->mysqli_qry = $sql;
		$this->mysqli_bind_types_str = $a_str;
		$this->mysqli_bind_array = $b_array;
		$this->mysqli_bind_result_array = $r_array;
		return true;
		}	# end of function

	function auto_p_and_b()
	{
		$this->prep_stmt();
		return $this->bind_stmt();
		}	# end of function

	function auto_do()
	{
		$this->prep_stmt();
		$this->bind_stmt();
		$this->exec_stmt();
		return $this->bind_result();
		}	# end of auto_do function

	function auto_init_p_and_b($sql, $a_str=null, $b_array=null, $r_array = null)
	{
		$this->auto_init($sql, $a_str, $b_array, $r_array);
		$this->prep_stmt();
		return $this->bind_stmt();
		}	# end of function

	function auto_init_and_do($sql, $a_str=null, $b_array=null, $r_array=null)
	{
		$this->auto_init($sql, $a_str, $b_array, $r_array);
		return $this->auto_do();
		}	# end of auto_init_and_do function

	function auto_e_and_b()
	{
		$this->exec_stmt();
		return $this->bind_result();
		}	# end of auto_e_and_b function

	private function qry_check()
	{
		if (strtolower(substr(trim($this->mysqli_qry), 0, 6)) == "insert" || strtolower(substr(trim($this->mysqli_qry), 0, 6)) == "delete"  || strtolower(substr(trim($this->mysqli_qry), 0, 7)) == "replace" || strtolower(substr(trim($this->mysqli_qry), 0, 6)) == "update")
		{
			$this->mysqli_do_store_result_bool = false;
			}	# End of main conditional
		else
		{
			$this->mysqli_do_store_result_bool = true;
			}
		return $this->mysqli_do_store_result_bool;
		}	# End of qry_check function

	function goto_row($seek=0)
	{
		if (is_object($this->mysqli_stmt_fo) )
		{
			$this->mysqli_goto_bool = $this->mysqli_stmt_fo->data_seek($seek);
			return $this->mysqli_goto_bool;
			}
		else
			{return false;}
		} # End of goto function

	function report($html=true)
	{
		$query_str = ($html) ? nl2br($this->mysqli_qry, false) : $this->mysqli_qry;
		$mt = 'microtime';
		$bind_str = "";

		foreach ($this->mysqli_bind_array as $bind_index=>$bind_val)
			{$bind_str .= " {$bind_index}=>{$bind_val}\n";}

		if ($html)
			{$bind_str = "<br>" . substr(nl2br($bind_str, false), 0, -4);}
		else
			{$bind_str = substr($bind_str, 0, -2);}

		$con_bool =  ($this->mysqli_con_bool) ? "Connected" : "Not connected";
		$con_err = ($this->mysqli_con_err !== NULL) ? "Con error: " . $this->mysqli_con_err : "No connection error";
		$store_bool = ($this->mysqli_do_store_result_bool) ? "Will store results" : "No results expected";
		$stmt_bool = ($this->mysqli_stmt_bool) ? "Statement prepped" : "Statement not prepped";
		$stmt_err = (!empty($this->mysqli_stmt_err) ) ? "Stmt error: " . $this->mysqli_stmt_err : "No statement error";
		$stmt_count = (!empty($this->mysqli_bind_array) ) ? "Stmt param count: " . count($this->mysqli_bind_array) : "No params in statement";
		$bind_type_str = ($this->mysqli_bind_types_str !== NULL && $this->mysqli_bind_types_str !== "") ? $this->mysqli_bind_types_str : "No params in query";
		$bind_bool = ($this->mysqli_bind_bool) ? "Statement bound \n$bind_str" : "Statement not bound";
		$bind_err = (!empty($this->mysqli_bind_err) ) ? "Stmt bind error: " . $this->mysqli_bind_err : "No binding error";
		$result_bool = ($this->mysqli_exec_result_bool) ? "Results available" : "No results available";
		$result_err = (!empty($this->mysqli_exec_result_err) ) ? "Exec results error: " . $this->mysqli_exec_result_err : "No execution error";
		$row_count = ($this->mysqli_exec_row_count !== NULL && $this->mysqli_exec_row_count > 0) ? "Exec row count affected: " . $this->mysqli_exec_row_count : "No rows affected";
		$num_rows = ($this->mysqli_exec_num_rows !== NULL && $this->mysqli_exec_num_rows > 0) ? "Exec num rows returned: " . $this->mysqli_exec_num_rows : "No rows returned";
		$insert_id = ($this->mysqli_insert_id != 0) ? "ID auto inserted: " . $this->mysqli_insert_id : "No ID auto inserted";
		$store_bool = ($this->mysqli_store_result_bool) ? "Results stored" : "No results stored";
		$bind_result = ($this->mysqli_bind_result_bool) ? "Results bound" : "No results bound";
		$bind_result_err = (!empty($this->mysqli_bind_result_err) ) ? "Bind results error: " . $this->mysqli_bind_result_err : "No results binding error";

		$bc = "background-color:#FFF;";
		$bce = "background-color:#CFC;";
		$start1 = "<tr>\n
    <td style=\"$bc \">";

		$start2 = "<tr>\n
    <td style=\"$bce \">";

		$mid1 = "&nbsp;</td>\n
    <td style=\"$bc \">";

		$mid2 = "&nbsp;</td>\n
    <td style=\"$bce \">";

		$end = "&nbsp;</td>\n
</tr>\n";

$class_exec_time = $mt(true)-$this->mysqli_start_time;

if ($html)
{
$table = <<<TABLE
<table id="mysqli_report" align="center" border="0">
<caption>DB Connection Report</caption>
<thead>
<tr>
    <th>Test</th>
    <th>Result</th>
</tr>
</thead>
<tbody>
{$start1}Connection{$mid1}$con_bool{$end}
{$start2}Connection Error{$mid2}$con_err{$end}
{$start1}Result Storage{$mid1}$store_bool{$end}
{$start2}Statement{$mid2}$stmt_bool{$end}
{$start1}Statement Error{$mid1}$stmt_err{$end}
{$start2}Parameter Count{$mid2}$stmt_count{$end}
{$start1}Bind Type String{$mid1}$bind_type_str{$end}
{$start2}Query{$mid2}$query_str{$end}
{$start1}Statement Binding{$mid1}$bind_bool{$end}
{$start2}Statement Binding Error{$mid2}$bind_err{$end}
{$start1}Results{$mid1}$result_bool{$end}
{$start2}Execution Error{$mid2}$result_err{$end}
{$start1}Update/Insert Rows Count (mysqli_exec_row_count){$mid1}$row_count{$end}
{$start2}Select Rows Returned Count (mysqli_exec_num_rows){$mid2}$num_rows{$end}
{$start1}Final Insert ID{$mid1}$insert_id{$end}
{$start2}Result Storage{$mid2}$store_bool{$end}
{$start1}Results Binding{$mid1}$bind_result{$end}
{$start2}Results Binding Error{$mid2}$bind_result_err{$end}
{$start1}Class Execution Time{$mid1}$class_exec_time{$end}
</tbody>
</table>
TABLE;

	$this->mysqli_report = "<br>$table<br>";
	}
else
{
$table = <<<TABLE
Connection: $con_bool
Connection Error: $con_err
Result Storage: $store_bool
Statement: $stmt_bool
Statement Error: $stmt_err
Parameter Count: $stmt_count
Bind Type String: $bind_type_str
Query:
$query_str

Statement Binding: $bind_bool
Statement Binding Error: $bind_err
Results: $result_bool
Execution Error: $result_err
Update/Insert Rows Count (mysqli_exec_row_count): $row_count
Select Rows Returned Count (mysqli_exec_num_rows): $num_rows
Final Insert ID: $insert_id
Result Storage: $store_bool
Results Binding: $bind_result
Results Binding Error: $bind_result_err
Class Execution Time: $class_exec_time\n\n\n
TABLE;

	$this->mysqli_report = $table;
	}

		} # End of report function

	function __Destruct()
	{
		$this->mysqli_con_bool = null;
		$this->mysqli_stmt_bool = null;	
		$this->mysqli_do_store_result_bool = null;
		$this->mysqli_bind_bool = null;
		$this->mysqli_exec_result_bool = null;
		$this->mysqli_store_result_bool = null;
		$this->mysqli_stmt_param_count = null;
		$this->mysqli_bind_result_allow_bool = null;
		$this->mysqli_bind_array = null;
		$this->mysqli_bind_result_array = null;
		$this->mysqli_qry = null;
		$this->mysqli_bind_types_str = null;
		$this->mysqli_bind_result_stmt = null;
		$this->mysqli_fetch_bool = null;
		$this->mysqli_goto_bool = null;
		$this->mysqli_insert_id = null;
		$this->mysqli_output = null;
		$this->mysqli_exec_meta_data = null;
		$this->mysqli_col = null;

		return true;
		}	# End of the destruct function

	} # End of mysqli object class
//_______________________________________________________________________________________
// Standard single row select result query
function hfw_row_pattern($query=null, $i=null, $input=null, $output=null)
{
	$value = array();

	$mo = new hfw_mysqli_obj('select', true);
	$mo->auto_init_and_do($query, $i, $input, $output);
	$mo->fetch();

	if (count($output) == 1 && array_key_exists($output[0], $mo->mysqli_output) )
		{$value = $mo->mysqli_output[$output[0]];}
	else
		{$value = $mo->mysqli_output;}
	unset($mo);

	return $value;
	}

//_______________________________________________________________________________________
// Standard multi-col select result query
function hfw_col_pattern($query=null, $i=null, $input=null, $output=null, $flatten=false)
{
	$value = array();

	$mo = new hfw_mysqli_obj('select', true);
	$mo->auto_init_and_do($query, $i, $input, $output);
	$mo->fetch_cols($output, $output, $flatten);

	$value = $mo->mysqli_col;
	unset($mo);

	return $value;
	}

//_______________________________________________________________________________________
// Standard transposed multi-col select result query
function hfw_tcol_pattern($query=null, $i=null, $input=null, $output=null)
{
	$value = array();

	$mo = new hfw_mysqli_obj('select', true);
	$mo->auto_init_and_do($query, $i, $input, $output);
	$mo->fetch_tcols($output);

	$value = $mo->mysqli_col;
	unset($mo);

	return $value;
	}

//_______________________________________________________________________________________
// Standard insert query
function hfw_ins_pattern($query=null, $i=null, $input=null, $output='mysqli_insert_id')
{
	$value = array();
	if (empty($output) ) {$output = 'mysqli_insert_id';}

	$mo = new hfw_mysqli_obj('add');
	$mo->auto_init_and_do($query, $i, $input);

	$value = $mo->$output;
	unset($mo);

	return $value;
	}

//_______________________________________________________________________________________
// Standard delete query
function hfw_del_pattern($query=null, $i=null, $input=null)
{
	$value = array();

	$mo = new hfw_mysqli_obj('delete');
	$mo->auto_init_and_do($query, $i, $input);

	$value = $mo->mysqli_exec_row_count;
	unset($mo);

	return $value;
	}

//_______________________________________________________________________________________
function transpose_output($input=array() )
{
	$o = array();

	foreach($input as $col=>$allrows)
	{
		if (is_array($allrows) )
		{
			foreach($allrows as $rowid=>$val)
			{
				$o[$rowid][$col] = $val;
				}
			}
		else
			{$o = $input;}
		}

	unset($input);

	return $o;
	}

//_______________________________________________________________________________________

?>
