<?php
/*
Copyright 2011-2024 Cargotrader, Inc. All rights reserved.

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

// page for creating common html objects
// The 2022 enhancements should be considered in-beta.
// The first fix in 2023 removed the ";\n" cobble bug due to iteration
// We've removed some of the restrictions and formatting on attributes, 
// which might result in unexpected behavior when tag swapping.
// The goal was to allow making adding more tags and attributes easier through 
// the pots call, and make some of the older code more in line with HTML 5.

class html_common
{
	public $output = null;
	protected $tag = null;
	protected $type = null;
	protected $name = null;
	protected $class = null;
	protected $id = null;
	protected $style = null;
	protected $title = null;
	protected $key = null;
	protected $tab = null;
	protected $labelfore = null;
	protected $label = null;
	protected $labelaft = null;
	protected $corefore = null;
	protected $core = null;
	protected $coreaft = null;
	protected $altcore = null;
	protected $fore = null;
	protected $aft = null;
	protected $open = null;
	protected $remainder = null;
	protected $for = null;
	protected $legend = null;
	protected $pot = null;

	function __construct($con=array() )
	{
		$this->common($con);
		}	# End of construct class

	public function common($con=array() )
	{
		$this->tag = (isset($con['tag']) ) ? $this->add_tag($con['tag']) : "<input ";
		unset($con['tag']);

		$insiders = array('type', 'name', 'class', 'id', 'style', 'title', 'for');
		$pots = array('value', 'class', 'max', 'min', 'form', 'maxlength', 'high', 'low', 'lang', 'optimum', 'src', 'alt', 'colspan', 'rowspan', 'align', 'valign', 'height', 'width', 'shape', 'href', 'cite', 'target', 'enctype', 'placeholder', 'accesskey', 'click', 'oninput', 'onclick', 'onchange', 'onselect', 'onmousedown', 'onmouseover', 'onkeydown', 'onkeypress', 'onkeyup', 'draggable', 'dropzone', 'role', 'step');
		$outsiders = array('label', 'labelfore', 'labelaft', 'core', 'corefore', 'coreaft', 'altcore', 'fore', 'aft');
		$abbrs = array('size', 'dis', 'req', 'open', 'ro');
		$samers = array('autofocus', 'checked', 'disabled', 'formnovalidate', 'multiple', 'novalidate', 'readonly', 'required', 'selected');
		$legends = array('legend', 'caption');

		foreach ($con as $conkey=>$conval)
		{
			if (in_array($conkey, $insiders) )
				{$this->$conkey = $this->add_common($conkey, $conval);}
			elseif ($conkey == 'ecore' || $conkey == 'altecore')
				{$this->core = $this->add_simple(ecore_object_output($conval) );}
			elseif (in_array($conkey, $pots) || substr($conkey, 0, 5) === 'data-' || substr($conkey, 0, 5) === 'aria-')
			{
				$this->$conkey = $this->add_common($conkey, $conval);
				$this->add_pot($this->$conkey);
				}
			elseif (in_array($conkey, $outsiders) )
				{$this->$conkey = $this->add_simple($conval);}
			elseif ($conkey == 'tab' && !is_array($conval) )
				{$this->$conkey = $this->add_tab( (int) $conval);}
			elseif ($conkey == 'uncommon')
				{$special = "add_$conkey"; $this->remainder = $this->$special($conval);}			
			elseif (in_array($conkey, $abbrs) )
				{$special = "add_$conkey"; $this->$conkey = $this->$special($conval);}			
			elseif (in_array($conkey, $samers) )
			{
				$this->$conkey = $this->add_same($conkey, $conval);
				$this->add_pot($this->$conkey);
				}
			elseif (in_array($conkey, $legends) )
				{$this->$conkey = $this->add_legend($conkey, $conval);}
			elseif ($conkey == 'key')
				{$this->key = $this->add_common('accesskey', $conval);}
			}	# End of foreach
		}	# End of common function

	public function add_common($attr=null, $val=null)
	{
		if ($this->check_not_blank($val) && !is_array($val) )
			{return "$attr=\"$val\" ";}
		return null;
		}

	public function add_simple($val=null)
	{
		if ($this->check_not_blank($val) && !is_array($val) )
			{return $val;}
		return null;
		}

	public function add_same($attr=null, $val=null)
	{
		if (isset($val) && !is_array($val) && $this->check_tf($val) )
			{return "$attr ";}
		return null;
		}

	public function add_legend($attr=null, $val=null)
	{
		if (isset($val) && !is_array($val) && $this->check_not_blank($val) )
			{return "<$attr>$val</$attr> ";}
		return null;
		}

	public function add_tag($tag=null)
	{
		if (!empty($tag) )
			{return "<$tag ";}

		return "<input ";
		}

	public function add_size($size=null)
	{
		if ($size > 0)
			{$this->add_pot("size=\"$size\" ");}		
		return null;
		}

	public function add_dis($dis=null)
	{
		$this->add_pot($this->add_same('disabled', $dis) );
		}

	public function add_req($req=null)
	{
		$this->add_pot($this->add_same('required', $req) );
		}

	public function add_ro($ro=null)
	{
		$this->add_pot($this->add_same('readonly', $ro) );
		}

	public function add_open($open=null)
	{
		if ($this->check_not_blank($open) && !is_array($open) )
			{return "open=\"true\" ";}
		return null;
		}

	public function add_tab($tab=null)
	{
		if ($tab > 0)
			{return "tabindex=\"$tab\" ";}
		return null;
		}

	public function add_uncommon($uncommon=array() )
	{
		$output = "";

		if (is_array($uncommon) && !empty($uncommon) && count($uncommon) > 0)
		{
			foreach ($uncommon as $type=>$value)
			{
				$temp = $this->add_item($type, $value);
				$output .= $temp;
				}
			}
		return $output;
		}

	public function trim_output($output=null, $tag=null)
	{
		if (trim($output) == "<$tag >") 
			{return "<$tag>";}
		else
			{return $output;}
		}

	public function add_item($type=null, $value=null)
	{
		if (!empty($type) && $this->check_not_blank($value) && !is_array($value) && !is_array($type) )
			{return "$type=\"$value\" ";}
		return null;
		}

	public function check_not_blank($val=null)
	{
		if ($val === 0) {return true;}
		if (is_numeric($val) ) {return true;}
		if (!empty($val) ) {return true;}
		return false;
		}

	public function check_tf($val=null)
	{
		if ($val === '0' || $val === 0 || $val === false || $val === NULL || $val === '' || $val === 'false' || $val === 'off') {return false;}
		return true;
		}

	public function add_pot($pot=null)
	{
		$this->pot .= $pot;
		}
	}	# End of html_common class

// ____________________________________________________________________________________________________
class generic_obj extends html_common
{
	public $objtag = 'div';
	public $objtype = '';
	public $closing_cap = '>';
	public $closing_tag = '';
	public $opening = '';
	public $bow = '';

	function __construct($con=array() )
	{
		$this->objtag = (isset($con['tag']) ) ? $con['tag'] : 'div';
		$this->path($con);
		return null;
		} # End of construct function

	public function path($con=array() )
	{
		$this->start($con);
		$this->output = $this->gen();
		return null;
		}

	public function way($con=array() )
	{
		$this->init($con);
		$this->output = $this->gen(null, false);
		}
		
	public function start($con=array() )
	{
		$con['tag'] = $this->objtag;
		$this->closing_tag = "</{$this->objtag}>";
		$this->common($con);
		}

	public function init($con=array() )
	{
		$con['type'] = $this->objtype;
		$this->closing_tag = null;
		$this->common($con);
		}

	public function bow($ins_attrs=null)
	{
		$output = "{$this->tag}{$this->type}{$this->name}$ins_attrs{$this->class}{$this->id}{$this->style}{$this->tab}{$this->title}{$this->for}{$this->key}{$this->open}{$this->pot}{$this->remainder}";

		$this->bow = $output;
		return null;
		}

	public function trim_bow()
	{
		$this->opening = $this->trim_output($this->bow . $this->closing_cap, $this->objtag);
		return null;
		}

	public function close_bow()
	{
		$this->opening = $this->bow . $this->closing_cap;
		return null;
		}

	public function cobble()
	{
		$output = "{$this->fore}{$this->opening}{$this->legend}{$this->corefore}{$this->core}{$this->coreaft}{$this->closing_tag}{$this->label}";

		// For historical reasons, the original parser used ';\n', which means $output can create an artificial ';\n' if it ends with a ';'
		if (!empty($output) && $output[-1] !== ';') {return $output . "\n" . $this->aft;} else {return $output . $this->aft;}
		}

	// The first parameter will add any additional attributes to the tag
	public function gen($ins_attrs=null, $trim=true)
	{
		$this->bow($ins_attrs);
		
		if ($trim) {$this->trim_bow();} else {$this->close_bow();}

		return $this->cobble();
		}

	public function remake($con)
	{
		$this->__construct($con);

		return $this->output;
		} # End of remake function

	function __destruct()
	{
		$this->output = null;
		$this->objtag = null;
		$this->closing_cap = null;
		$this->closing_tag = null;
		$this->opening = null;
		$this->bow = null;
		$this->pot = null;
		}
	}	# End of generic_obj class

// ____________________________________________________________________________________________________
class label_obj extends generic_obj
{
	public $objtag = "label";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of label_obj class

// ____________________________________________________________________________________________________
class p_obj extends generic_obj
{
	public $objtag = "p";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		}
	}	# End of p_obj class

// ____________________________________________________________________________________________________
class q_obj extends generic_obj
{
	public $objtag = "q";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		}
	}	# End of q_obj class

// ____________________________________________________________________________________________________
class i_obj extends generic_obj
{
	public $objtag = "i";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of i_obj class

// ____________________________________________________________________________________________________
class li_obj extends generic_obj
{
	public $objtag = "li";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of li_obj class

// ____________________________________________________________________________________________________
class ol_obj extends generic_obj
{
	public $objtag = "ol";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of ol_obj class

// ____________________________________________________________________________________________________
class ul_obj extends generic_obj
{
	public $objtag = "ul";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of ul_obj class

// ____________________________________________________________________________________________________
class dl_obj extends generic_obj
{
	public $objtag = "dl";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of dl_obj class

// ____________________________________________________________________________________________________
class dt_obj extends generic_obj
{
	public $objtag = "dt";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of dt_obj class

// ____________________________________________________________________________________________________
class dd_obj extends generic_obj
{
	public $objtag = "dd";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of dd_obj class

// ____________________________________________________________________________________________________
class h1_obj extends generic_obj
{
	public $objtag = "h1";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of h1_obj class

// ____________________________________________________________________________________________________
class h2_obj extends generic_obj
{
	public $objtag = "h2";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of h2_obj class

// ____________________________________________________________________________________________________
class h3_obj extends generic_obj
{
	public $objtag = "h3";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of h3_obj class

// ____________________________________________________________________________________________________
class h4_obj extends generic_obj
{
	public $objtag = "h4";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of h4_obj class

// ____________________________________________________________________________________________________
class h5_obj extends generic_obj
{
	public $objtag = "h5";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of h5_obj class

// ____________________________________________________________________________________________________
class nav_obj extends generic_obj
{
	public $objtag = "nav";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of nav_obj class

// ____________________________________________________________________________________________________
class header_obj extends generic_obj
{
	public $objtag = "header";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of header_obj class

// ____________________________________________________________________________________________________
class footer_obj extends generic_obj
{
	public $objtag = "footer";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of footer_obj class

// ____________________________________________________________________________________________________
class div_obj extends generic_obj
{
	public $objtag = "div";

	function __construct($con=array() )
	{
		$this->start($con);
		$this->output = $this->gen();
		return null;
		}

	// Normally, a div or extended classes shall close automatically if the core tag is not blank.
	// Likewise, a blank core tag in $con will keep the div or extended classes open as in <div>...
	// We allow the div or extended classes to remain open with the 'noclose' tag in $con, even if core is not blank.
	// If desired, an empty core div or extended classes can be forced to close with the close tag in $con.
	public function start($con=array() )
	{
		$con['tag'] = $this->objtag;
		$this->common($con);
		$this->closing_tag = "</{$this->objtag}>";
		
		if ( ($this->core === Null && !isset($con['close']) ) || ($this->core !== Null && isset($con['noclose']) ) )
			{$this->closing_tag = null;}
		
		return null;
		}
	}	# End of div_obj class

// ____________________________________________________________________________________________________
class span_obj extends div_obj
{
	public $objtag = "span";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		}
	}	# End of span_obj class

// ____________________________________________________________________________________________________
class details_obj extends div_obj
{
	public $objtag = "details";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		}
	}	# End of details_obj class

// ____________________________________________________________________________________________________
class summary_obj extends div_obj
{
	public $objtag = "summary";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		}
	}	# End of summary_obj class

// ____________________________________________________________________________________________________
class code_obj extends div_obj
{
	public $objtag = "code";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		}
	}	# End of code_obj class

// ____________________________________________________________________________________________________
class mark_obj extends div_obj
{
	public $objtag = "mark";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		}
	}	# End of mark_obj class

// ____________________________________________________________________________________________________
class picture_obj extends div_obj
{
	public $objtag = "picture";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		}
	}	# End of mark_obj class

// ____________________________________________________________________________________________________
class form_obj extends div_obj
{
	public $objtag = "form";

	function __construct($con=array() )
	{
		$this->start($con);

		// We set non-null default values for action and method to allow for easy shorthand form, as in $aoo('form')
		$action = (isset($con['action']) ) ? $this->add_common('action', $con['action']) : 'action="" ';
		$method = (isset($con['method']) ) ? $this->add_common('method', $con['method']) : 'method="post" ';

		$this->output = $this->gen("$action{$method}");
		return null;
		}
	}	# End of form_obj class

// ____________________________________________________________________________________________________
class fieldset_obj extends div_obj
{
	public $objtag = "fieldset";

	function __construct($con=array() )
	{
		// We offer the option to use caption or legend for con, swapping caption for legend if not there.
		// Otherwise, we remove any caption entry.
		if (!isset($con['legend']) && isset($con['caption']) && $this->check_not_blank($con['caption']) )
			{$con['legend'] = $con['caption']; unset($con['caption']);}
		elseif (isset($con['caption']) )
			{unset($con['caption']);}
			
		$this->start($con);

		$this->output = $this->gen();
		return null;
		}
	}	# End of fieldset_obj class

// ____________________________________________________________________________________________________
class optgroup_obj extends div_obj
{
	public $objtag = "optgroup";

	function __construct($con=array() )
	{
		$this->start($con);

		// Non-standard use of label must be dealt with
		$label = (isset($con['label']) ) ? $this->add_common('label', $con['label']) : null;
		$this->label = null;

		$this->output = $this->gen($label);
		return null;
		} # End of construct function
	}	# End of optgroup_obj class

// ____________________________________________________________________________________________________
class textbox_obj extends generic_obj
{
	public $objtype = 'text';

	function __construct($con=array() )
	{
		$this->way($con);
		return null;
		}
	} # End of textbox_obj class

// ____________________________________________________________________________________________________
class password_obj extends textbox_obj
{
	public $objtype = 'password';

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		}
	}	# End of password obj class

// ____________________________________________________________________________________________________
class textarea_obj extends generic_obj
{
	public $objtag = "textarea";

	function __construct($con=array() )
	{
		// We need to swap value with core to have the newer syntax work
		// ecore would explicitly be called, so we don't worry about that case (mistaking 'value' for 'ecore').
		if (isset($con['value']) && !isset($con['core']) && !isset($con['ecore']) ) {$con['core'] = $con['value'];}
		unset($con['value']);
		
		$cols = (isset($con['cols']) ) ? $this->add_sizing('cols', (int) $con['cols']) : null;
		$rows = (isset($con['rows']) ) ? $this->add_sizing('rows', (int) $con['rows']) : null;
		unset($con['cols']);
		unset($con['rows']);
		
		$this->start($con);

		$this->output = $this->gen("$cols$rows");
		return null;
		}

	public function add_sizing($dir=null, $val=null)
	{
		if (check_index($val) )
			{return "$dir=\"$val\" ";}		
		return null;
		}
	} # End of textarea obj class

// ____________________________________________________________________________________________________
class hidden_obj extends generic_obj
{
	public $objtype = 'hidden';

	function __construct($con=array() )
	{
		$this->way($con);
		return null;
		}
	}	# End of hidden_obj class

// ____________________________________________________________________________________________________
class number_obj extends generic_obj
{
	public $objtype = 'number';

	function __construct($con=array() )
	{
		// We will start with the dval if forced to, though the number then can't be blank
		if ( (!isset($con['value']) || !$this->check_not_blank($con['value']) ) && isset($con['dval']) && $this->check_not_blank($con['dval']) )
			{$con['value'] = $con['dval'];}
			
		$this->way($con);
		return null;
		}
	}	# End of number_obj class

// ____________________________________________________________________________________________________
class range_obj extends generic_obj
{
	public $objtype = 'range';

	function __construct($con=array() )
	{
		$this->way($con);
		return null;
		}
	}	# End of range_obj class

// ____________________________________________________________________________________________________
class month_obj extends generic_obj
{
	public $objtype = 'month';

	function __construct($con=array() )
	{
		$this->way($con);
		return null;
		}
	}	# End of month_obj class

// ____________________________________________________________________________________________________
class date_obj extends generic_obj
{
	public $objtype = 'date';

	function __construct($con=array() )
	{
		$this->way($con);
		return null;
		}
	}	# End of date_obj class

// ____________________________________________________________________________________________________
class dtlocal_obj extends generic_obj
{
	public $objtype = 'datetime-local';

	function __construct($con=array() )
	{
		$this->way($con);
		return null;
		}
	}	# End of dtlocal_obj class

// ____________________________________________________________________________________________________
class color_obj extends generic_obj
{
	public $objtype = 'color';

	function __construct($con=array() )
	{
		$this->way($con);
		return null;
		}
	}	# End of color_obj class

// ____________________________________________________________________________________________________
class search_obj extends generic_obj
{
	public $objtype = 'search';

	function __construct($con=array() )
	{
		$this->way($con);
		return null;
		}
	}	# End of search_obj class

// ____________________________________________________________________________________________________
class tel_obj extends generic_obj
{
	public $objtype = 'tel';

	function __construct($con=array() )
	{
		if (isset($con['pattern']) && isset($con['pattern']) )
		{
			$con['uncommon']['pattern'] = $con['pattern'];
			}

		$this->way($con);
		return null;
		}
	}	# End of tel_obj class

// ____________________________________________________________________________________________________
class time_obj extends generic_obj
{
	public $objtype = 'time';

	function __construct($con=array() )
	{
		$this->way($con);
		return null;
		}
	}	# End of time_obj class

// ____________________________________________________________________________________________________
class url_obj extends generic_obj
{
	public $objtype = 'url';

	function __construct($con=array() )
	{
		$this->way($con);
		return null;
		}
	}	# End of url_obj class

// ____________________________________________________________________________________________________
class hr_obj extends generic_obj
{
	public $objtype = 'hr';

	function __construct($con=array() )
	{
		$con['tag'] = $this->objtype;
		$this->closing_tag = null;
		$this->common($con);
		$this->output = $this->gen(null, false);
		return null;
		}
	}	# End of hr_obj class

// ____________________________________________________________________________________________________
class output_obj extends generic_obj
{
	public $objtype = 'output';

	function __construct($con=array() )
	{
		$this->way($con);
		return null;
		}
	}	# End of output_obj class

// ____________________________________________________________________________________________________
class input_button_obj extends generic_obj
{
	public $objtype = 'button';

	function __construct($con=array() )
	{
		$this->way($con);
		return null;
		}	# End of construct function
	}	# End of input_button_obj class

// ____________________________________________________________________________________________________
class cb_obj extends generic_obj
{
	public $objtype = 'checkbox';

	function __construct($con=array() )
	{
		// If not already parsed by __r__, both tval and dval for cb_objs can be arrays with the string pattern val1,,val2,,val3...
		// This can make manual entries easier to make
		// We decompose them into arrays if they exist.
		// There can only be assumed to be one value, however.
		// For radio buttons we must assume that dval and tval are singular element arrays
		
		$val = (isset($con['value']) && $con['value'] !== '') ? $con['value'] : false;
		$dval = (isset($con['dval']) && !is_array($con['dval']) && $con['dval'] !== '') ? explode(',,', $con['dval']) : ( (isset($con['dval']) && is_array($con['dval']) ) ? $con['dval'] : false);
		$tval = (isset($con['tval']) && !is_array($con['tval']) && $con['tval'] !== '') ? explode(',,', $con['tval']) : ( (isset($con['tval']) && is_array($con['tval']) ) ? $con['tval'] : false);

		if ($this->objtype == 'radio')
		{
			$dval = (is_array($dval) && isset($dval[0]) ) ? array($dval[0]) : $dval;
			$tval = (is_array($tval) && isset($tval[0]) ) ? array($tval[0]) : $tval;
			}
			
		// This sets up a priority for deciding if tval (or the returned true value) shall apply.
		// The tval must exist and the val must exist, and then they must be the same.
		// If they are the same then tf is set to true (checked="checked").
		// If they are different then dval (the default value) will be checked.
		// The dval must exist and the val must exist, and then they must be the same.
		// If they are different then con['tf'] will be checked.
		// Next, the tf will be set via the forcing setting con['tf'] (t or f).
		// If con['tf'] does not exist, then we can use the dtf, or default tf value.
		// con['tf'] can be set by the environment of returned values.
		// con['dtf'] should only be set before any values are returned.
						
		$c = 'checked';
		
		if ($val !== false && $tval !== false && in_array($val, $tval) ) {$tf = $this->add_same($c, 1);}
		elseif ($val !== false && $tval !== false && !in_array($val, $tval) ) {$tf = null;}
		elseif ($val !== false && $dval !== false && in_array($val, $dval) ) {$tf = $this->add_same($c, 1);}
		elseif (isset($con['tf']) && $con['tf'] !== '') {$tf = $this->add_same($c, $con['tf']);}
		elseif (isset($con['dtf']) ) {$tf = $this->add_same($c, $con['dtf']);}
		else {$tf = null;}

		$this->init($con);
		
		$this->label = (isset($con['label']) ) ? $this->add_simple($this->labelfore . $this->label . $this->labelaft) : null;

		$this->output = $this->gen("$tf", false);
		return null;
		}
	}	# End of cb_obj class

// ____________________________________________________________________________________________________
class checkbox_obj extends cb_obj
{
	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		}
	}	# End of checkbox_obj class

// ____________________________________________________________________________________________________
class rb_obj extends cb_obj
{
	public $objtype = 'radio';

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		}
	}	# End of rb_obj class

// ____________________________________________________________________________________________________
class radio_obj extends rb_obj
{
	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		}
	}	# End of radio_obj class

// ____________________________________________________________________________________________________
// For HTML5 meters
class meter_obj extends generic_obj
{
	public $objtag = "meter";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		}	# End of construct function
	}	# End of meter_obj class

// ____________________________________________________________________________________________________
class image_obj extends generic_obj
{
	// This deprecated tag might be needed for SVG support.
	// Otherwise, use img.
	
	public $objtype = 'image';

	function __construct($con=array() )
	{
		// Note that image supports a "value", whereas img does not.
		$this->way($con);
		return null;
		}	# End of construct function
	} # End of image_obj class

// ____________________________________________________________________________________________________
class canvas_obj extends generic_obj
{
	public $objtag = 'canvas';

	function __construct($con=array() )
	{
		$alt = (isset($con['alt']) ) ? $con['core'] = $con['alt'] : null;
		unset($con['alt']);
		
		$this->start($con);

		$this->output = $this->gen(null, false);
		return null;
		}	# End of construct function
	}	# End of canvas_obj class

// ____________________________________________________________________________________________________
class style_obj extends generic_obj
{
	public $objtag = 'style';
	public $objtype = 'text/css';

	function __construct($con=array() )
	{
		$con['type'] = $this->objtype;
		$this->start($con);

		$this->output = $this->gen(null, false);
		return null;
		}	# End of construct function
	} # End of style_obj class

// ____________________________________________________________________________________________________
class script_obj extends generic_obj
{
	public $objtag = 'script';

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		}	# End of construct function
	} # End of script_obj class

// ____________________________________________________________________________________________________
class js_obj extends script_obj
{
	public $objtype = 'text/javascript';

	function __construct($con=array() )
	{
		$con['type'] = $this->objtype;
		parent::__construct($con);
		return null;
		}
	}	# End of js_obj class

// ____________________________________________________________________________________________________
class file_obj extends generic_obj
{
	public $objtype = 'file';

	function __construct($con=array() )
	{
		$this->init($con);

		$accept = (isset($con['accept']) ) ? $this->add_common('accept', $con['accept']) : null;

		$this->output = $this->gen("$accept", false);
		return null;
		}	# End of construct function
	}	# End of file_obj class

// ____________________________________________________________________________________________________
class button_obj extends generic_obj
{
	public $objtag = "button";

	function __construct($con=array() )
	{
		// Default button type is 'submit'
		if (!isset($con['type']) ) {$con['type'] = 'submit';}

		$this->start($con);

		// Non-standard use of label must be dealt with
		$this->core = (isset($con['label']) ) ? $con['label'] : $this->core;
		$this->label = null;

		$this->output = $this->gen(null, false);
		return null;
		}
	}	# End of button_obj class

// ____________________________________________________________________________________________________
class reset_obj extends generic_obj
{
	public $objtag = "button";

	function __construct($con=array() )
	{
		$con['type'] = 'reset';

		$this->start($con);

		// Non-standard use of label must be dealt with
		$this->core = (isset($con['label']) ) ? $con['label'] : ( ($this->core !== Null) ? $this->core : "Reset");
		$this->label = null;

		$this->output = $this->gen(null, false);
		return null;
		}
	}	# End of reset_obj class

// ____________________________________________________________________________________________________
class clear_obj extends generic_obj
{
	public $objtype = "reset";

	function __construct($con=array() )
	{
		$this->way($con);
		return null;
		}
	}	# End of clear_obj class

// ____________________________________________________________________________________________________
class email_obj extends generic_obj
{
	public $objtype = "email";

	function __construct($con=array() )
	{
		$this->way($con);
		return null;
		}
	}	# End of email_obj class

// ____________________________________________________________________________________________________
// The following class is for "a" links, not HTML include files that use a "<link" tag, see below for other options
class link_obj extends generic_obj
{
	public $objtag = "a";

	function __construct($con=array() )
	{
		$this->start($con);

		$newpg = (isset($con['newpg']) || isset($con['blank'])  ) ? "target=\"_blank\" " : null;

		// We need to account for the deprecated use of 'label'
		if ($this->core === NULL && $this->label !== NULL) {$this->core = $this->label; $this->label = null;}

		$this->output = $this->gen($newpg, false);
		return null;
		}	# End of construct function
	}	# End of link_obj class

// ____________________________________________________________________________________________________
// This class give a more traditional call to create a hyperlink
class a_obj extends link_obj
{
	public $objtag = "a";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		}	# End of construct function
	}	# End of a_obj class

// ____________________________________________________________________________________________________
// This class allows users to use hlink when calling to create a hyperlink
class hlink_obj extends link_obj
{
	public $objtag = "a";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		}	# End of construct function
	}	# End of hlink_obj class

// ____________________________________________________________________________________________________
// The following class is for HTML include "link" tags, not hyperlinks
class inc_obj extends generic_obj
{
	public $objtag = "link";

	function __construct($con=array() )
	{
		$this->init($con);

		$media = (isset($con['media']) ) ? $this->add_common('media', $con['media']) : null;
		
		// We default to stylesheet or use 'css' to make it easier
		if (!isset($con['rel']) || $con['rel'] == 'css') {$con['rel'] == 'stylesheet';}
		$rel = $this->add_common('rel', $con['rel']);

		$this->output = $this->gen("$rel$media", false);
		return null;
		}	# End of construct function
	}	# End of inc_obj class

// ____________________________________________________________________________________________________
class address_obj extends generic_obj
{
	public $objtag = "address";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		}	# End of construct function
	}	# End of address_obj class

// ____________________________________________________________________________________________________
// This is for html5 blockquotes
class blockquote_obj extends generic_obj
{
	public $objtag = "blockquote";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		}	# End of construct function
	}	# End of blockquote_obj class

// ____________________________________________________________________________________________________
// This is alternative for html5 blockquotes
class bq extends blockquote_obj
{
	public $objtag = "blockquote";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		}	# End of construct function
	}	# End of bq_obj class

// ____________________________________________________________________________________________________
class img_obj extends generic_obj
{
	public $objtag = "img";

	function __construct($con=array() )
	{
		$con['tag'] = $this->objtag;
		
		$this->way($con);
		return null;
		}	# End of construct function
	}	# End of img_obj class

// ____________________________________________________________________________________________________
class source_obj extends generic_obj
{
	public $objtag = "source";

	function __construct($con=array() )
	{
		$con['tag'] = $this->objtag;
		
		$this->init($con);

		$media = (isset($con['media']) ) ? $this->add_common('media', $con['media']) : null;
		$srcset = (isset($con['srcset']) ) ? $this->add_common('srcset', $con['srcset']) : null;
		
		$this->output = $this->gen("$media$srcset", false);
		return null;
		}	# End of construct function
	}	# End of img_obj class

// ____________________________________________________________________________________________________
class meta_obj extends generic_obj
{
	public $objtag = "meta";

	function __construct($con=array() )
	{
		$con['tag'] = $this->objtag;
		$this->init($con);

		$charset = (isset($con['charset']) ) ? $this->add_common('charset', $con['charset']) : null;
		$property = (isset($con['property']) ) ? $this->add_common('property', $con['property']) : null;
		// We shorten http-equiv to 'he' for convenience.
		$httpequiv = (isset($con['he']) ) ? $this->add_common('http-equiv', $con['he']) : null;
		// We'll accept value for content
		$content = (isset($con['content']) ) ? $this->add_common('content', $con['content']) : ( (isset($con['value']) ) ? $this->add_common('content', $con['value']) : null);

		$this->output = $this->gen("$property$charset{$httpequiv}{$content}", false);
		return null;
		}	# End of construct function
	}	# End of meta_obj class

// ____________________________________________________________________________________________________
class select_obj extends generic_obj
{
	public $objtag = "select";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of select_obj class

// ____________________________________________________________________________________________________
class iframe_obj extends generic_obj
{
	public $objtag = "iframe";

	function __construct($con=array() )
	{
		$this->start($con);

		if (!isset($con['frameborder']) || $con['frameborder'] === NULL) {$con['frameborder'] = 0;}
		$frameborder = $this->add_common('frameborder', $con['frameborder']);
		$scrolling = (isset($con['scrolling']) ) ? $this->add_common('scrolling', $con['scrolling']) : null;
		if (!empty($con['fullscreen']) ) {$fullscreen = "webkitAllowFullScreen allowFullScreen";} else {$fullscreen = null;}

		$this->output = $this->gen("{$frameborder}{$scrolling}{$fullscreen}", false);
		return null;
		}	# End of construct function
	}	# End of iframe_obj class

// ____________________________________________________________________________________________________
class audio_obj extends generic_obj
{
	public $objtag = "audio";

	function __construct($con=array() )
	{
		$this->start($con);

		$preload = (isset($con['preload']) ) ? 'preload="' . $con['preload'] . '" ' : null;
		$autoplay = (isset($con['autoplay']) ) ? 'autoplay="autoplay" ' : null;
		$controls = (isset($con['controls']) ) ? 'controls="controls" ' : null;
		$loop = (isset($con['loop']) ) ? 'loop="loop" ' : null;

		$this->output = $this->gen("{$preload}{$autoplay}$controls{$loop}", false);
		return null;
		}
	}	# End of audio_obj class

// ____________________________________________________________________________________________________
class table_obj extends generic_obj
{
	public $objtag = "table";

	function __construct($con=array() )
	{
		// We offer the option to use caption or legend for con, swapping legend for caption if caption is not set.
		// We unset legend if caption is set.
		if (!isset($con['caption']) && isset($con['legend']) && $this->check_not_blank($con['legend']) )
			{$con['caption'] = $con['legend']; unset($con['legend']);}
		elseif (isset($con['legend']) )
			{unset($con['legend']);}
			
		$this->start($con);

		$border = (isset($con['border']) ) ? $this->add_common('border', $con['border']) : null;
		$frame = (isset($con['frame']) ) ? $this->add_common('frame', $con['frame']) : null;
		$width = (isset($con['width']) ) ? $this->add_common('width', $con['width']) : null;
		$cellpadding = (isset($con['cellpadding']) ) ? $this->add_common('cellpadding', $con['cellpadding']) : null;
		$cellspacing = (isset($con['cellspacing']) ) ? $this->add_common('cellspacing', $con['cellspacing']) : null;
		$rules = (isset($con['rules']) ) ? $this->add_common('rules', $con['rules']) : null;

		$this->output = $this->gen("{$border}$frame{$width}{$cellpadding}{$cellspacing}$rules");
		return null;
		} # End of construct function
	}	# End of table_obj class

// ____________________________________________________________________________________________________
class td_obj extends generic_obj
{
	public $objtag = "td";

	function __construct($con=array() )
	{
		$this->path($con);
		return null;
		} # End of construct function
	}	# End of td_obj class

// ____________________________________________________________________________________________________
class th_obj extends td_obj
{
	public $objtag = "th";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		} # End of construct function
	}	# End of th_obj class

// ____________________________________________________________________________________________________
class tr_obj extends td_obj
{
	public $objtag = "tr";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		} # End of construct function
	}	# End of tr_obj class

// ____________________________________________________________________________________________________
class thead_obj extends td_obj
{
	public $objtag = "thead";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		} # End of construct function
	}	# End of thead_obj class

// ____________________________________________________________________________________________________
class tfoot_obj extends td_obj
{
	public $objtag = "tfoot";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		} # End of construct function
	}	# End of tfoot_obj class

// ____________________________________________________________________________________________________
class tbody_obj extends td_obj
{
	public $objtag = "tbody";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		} # End of construct function
	}	# End of tbody_obj class

// ____________________________________________________________________________________________________
class caption_obj extends td_obj
{
	public $objtag = "caption";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		} # End of construct function
	}	# End of caption_obj class

// ____________________________________________________________________________________________________
class legend_obj extends td_obj
{
	public $objtag = "legend";

	function __construct($con=array() )
	{
		parent::__construct($con);
		return null;
		} # End of construct function
	}	# End of legend_obj class

// ____________________________________________________________________________________________________
class array_common extends generic_obj
{
	public function find_tval($value=null, $tval=null, $dval=null)
	{
		// If the tval is the selected or 'true' value, the dval is the default selected value
		if ($tval === NULL && $dval !== NULL) {$tval = $dval;}
		elseif (is_array($tval) && is_array($dval) && empty($tval) && !empty($dval) ) {$tval = $dval;}
		
		unset($dval);

		if (is_array($value) )
		{
			$tf = array();

			foreach ($value as $key=>$val)
			{
				if (is_array($tval) && in_array($val, $tval) )
					{$tf[$key] = true;}
				elseif (!is_array($tval) && $val == $tval)
					{$tf[$key] = true;}
				else
					{$tf[$key] = false;}
				}
			}
		else
		{
			if (is_array($tval) && in_array($value, $tval) )
				{$tf = true;}
			elseif (!is_array($tval) && $value == $tval)
				{$tf = true;}
			else
				{$tf = false;}
			}
		return $tf;
		}	# End of find_tval function
		
	public function handle_array($con=array(), $type='option', $use_goo=true)
	{
		// This function simplifies creating arrays of objects
		$use_goo = force_boolean($use_goo, true);
		
		if (!in_array($type, array('cb', 'rb', 'option') ) ) {$type = 'option';}
		
		// We do a survey of con to see which elements are also arrays
		// We store the non-array elements in the flat array
		$flat = array();
		$mult = array();
		
		foreach ($con as $key=>$elem)
		{
			if (is_array($elem) && count($elem) > 0)
				{$mult[$key] = $elem;}
			else
				{$flat[$key] = $elem;}
			}
			
		// We need to remove fore and aft if they are in $flat
		if (isset($flat['fore']) ) {unset($flat['fore']);}
		
		if (isset($flat['aft']) ) {unset($flat['aft']);}
		
		// There might be the case of only one row, everything flat
		if (count($mult) > 0)
		{
			// We need to assume the $mult keys have the same count size.
			// We can combine them into single elem arrays.
			// We get the keys of $mult with array_keys.
			// We then get the first value of $mult_keys ($first_key) with array_shift, 
			// Which also shrinks the list by one.
			$mult_keys = array_keys($mult);
			$first_key = array_shift($mult_keys);
			
			// We foreach them to create an array of flat arrays
			$new_mult = array();
			$row = array();
			
			foreach ($mult[$first_key] as $i=>$val)
			{
				$row[$first_key] = $val;
				
				foreach ($mult_keys as $k=>$key)
				{
					$row[$key] = $mult[$key][$i];
					}
					
				if ($use_goo) {$new_mult[] = gen_obj_output($type, array_merge($flat, $row) );}
				else {$new_mult[] = array_merge($flat, $row);}
				$row = array();
				}
			return $new_mult;
			}
		else 
		{
			if ($use_goo) {return array(gen_obj_output($type, $flat) );}
			return array($flat);
			}
		}	# End of handle_array() function
	}	# End of array_common class

// ____________________________________________________________________________________________________
class cb_array_obj extends array_common
{
	public $objtype = 'checkbox';

	function __construct($con=array() )
	{
		// This is a complete revamping of this call
		// We are going to rely on flattening of cb_array2 for more flexibility
		
		// We pull out fore and aft first
		$fore = null;
		$aft = null;
		
		if (isset($con['fore']) && !is_array($con['fore']) )
		{
			$fore = $con['fore'];
			}
		
		if (isset($con['aft']) && !is_array($con['aft']) )
		{
			$aft = $con['aft'];
			}

		$this->output = $fore . implode('', gen_obj_output('cb_array2', $con) ) . $aft;
		}	# End of construct function
	}	# End of cb_array_obj class

// ____________________________________________________________________________________________________
class cb_array2_obj extends array_common
{
	public $objtype = 'checkbox';

	function __construct($con=array() )
	{
		// This outputs an array, with fore and aft removed if in $flat.
		// This will assume the $use_goo is true
		$this->output = $this->handle_array($con, 'cb');
		}	# End of function __construct
	}	# End of cb_array2_obj class

// ____________________________________________________________________________________________________
class rb_array2_obj extends array_common
{
	public $objtype = 'radio';

	function __construct($con=array() )
	{
		// This outputs an array, with fore and aft removed if in $flat.
		// This will assume the $use_goo is true
		$this->output = $this->handle_array($con, 'rb');
		}	# End of function __construct
	}	# End of cb_array2_obj class

// ____________________________________________________________________________________________________
class rb_array_obj extends array_common
{
	public $objtype = 'radio';

	function __construct($con=array() )
	{
		// This is a complete revamping of this call
		// We are going to rely on flattening of rb_array2 for more flexibility
		
		// We pull out fore and aft first
		$fore = null;
		$aft = null;
		
		if (isset($con['fore']) && !is_array($con['fore']) )
		{
			$fore = $con['fore'];
			}
		
		if (isset($con['aft']) && !is_array($con['aft']) )
		{
			$aft = $con['aft'];
			}
			
		$this->output = $fore . implode(null, gen_obj_output('rb_array2', $con) ) . $aft;
		}	# End of construct function
	}	# End of rb_array_obj class

// ____________________________________________________________________________________________________
class option_obj extends array_common
{
	public $objtag = "option";

	function __construct($con=array() )
	{
		// This is a complete revamping of this call
		// We now rely on the handle array function
		
		// We pull out fore and aft first
		if (isset($con['fore']) ) {unset($con['fore']);}
		
		if (isset($con['aft']) ) {unset($con['aft']);}
		
		// We steal the marklist and mark since it will probably be a subset of the total list
		$marklist = array();
		$mark = "&bull;&nbsp;";
		
		if (isset($con['marklist']) ) 
		{
			$marklist = $con['marklist'];
			unset($con['marklist']);
			}

		if (isset($con['mark']) ) 
		{
			$mark = $con['mark'];
			unset($con['mark']);
			}

		// We need to swap label, labelfore and labelaft with core, corefore and coreaft since that is the newer syntax for the option tag and other tags
		// It doesn't make much sense to use ecore here, but we account for it.
		if (isset($con['label']) && !isset($con['core']) && !isset($con['ecore']) ) {$con['core'] = $con['label'];}
		if (isset($con['labelfore']) && !isset($con['corefore']) ) {$con['corefore'] = $con['labelfore'];}
		if (isset($con['labelaft']) && !isset($con['coreaft']) ) {$con['coreaft'] = $con['labelaft'];}
		
		unset($con['label']);
		unset($con['labelfore']);
		unset($con['labelaft']);

		// handle_array takes con and makes an array of objects
		$obj_set = $this->handle_array($con, null, false);

		$conkey = 'selected';

		foreach ($obj_set as $key=>$obj)
		{
			// We deal with the marklist.  We can use find_tval to return true or false.
			// To make the mark blank, refrain from using marklist or set mark blank, as for example, mark=;\nmarklist=$blah
			if (!empty($marklist) )
			{
				if ($this->find_tval($obj['value'], $marklist) )
					{$obj['corefore'] = (isset($obj['corefore']) ) ? $mark . $obj['corefore'] : $mark;}
				}
			
			$this->start($obj);

			// If the tval is the selected or 'true' value, the dval is the default selected value
			$tval = (isset($obj['tval']) && $this->check_not_blank($obj['tval']) ) ? $obj['tval'] : null;
			$dval = (isset($obj['dval']) && $this->check_not_blank($obj['dval']) ) ? $obj['dval'] : null;

			// In theory, since $tval and $dval can be arrays, multi selects should work.
			// For single values, find_tval will return true or false.
			// So, if the object is not already marked by 'selected' then it will mark it selected with true.
			if (!isset($obj[$conkey]) )
				{$this->add_pot($this->add_same($conkey, $this->find_tval($obj['value'], $tval, $dval) ) );}
				
			$this->output .= $this->gen();
			$this->pot = null;
			unset($obj['corefore']);
			$this->corefore = null;
			}	# End of foreach
		unset($obj_set);
		unset($con);
		}	# End of construct function
	}	# End of option_obj class

// ____________________________________________________________________________________________________
class tag_obj extends generic_obj
{
	public $objtag = "tag";

	function __construct($con=array() )
	{
		// We can create any generic_obj tag missing from the master list above
		if (is_array($con) )
		{
			if (isset($con['tagtype']) )
			{
				$this->objtag = $con['tagtype'];
				$this->path($con);
				}
			}
		return null;
		} # End of construct function
	}	# End of h3_obj class

// ____________________________________________________________________________________________________
class string_obj extends generic_obj
{
	function __construct($con=array() )
	{
		$this->common($con);
		$this->output = $this->local_gen($con);
		}	# End of construct function

	public function local_gen($con=array() )
	{
		// This allows us to switch between active and passive elements with just a change in the tag, ignoring all other active settings.
		// Core and Value are preserved as a string to be echoed, along with the fore and aft values.
		$value = (isset($con['value']) ) ? $con['value'] : null;

$output = <<<OUTPUT
{$this->fore}{$this->core}{$value}{$this->aft}
OUTPUT;

		return $output;
		}
	}	# End of string_obj class

// ____________________________________________________________________________________________________
class truefalse_obj extends generic_obj
{
	function __construct($con=array() )
	{
		$this->common($con);
		$this->output = $this->local_gen($con);
		}	# End of construct function

	public function local_gen($con=array() )
	{
		// A truefalse object requires the tf value to be 0  or 1.
		// A value of 1 selects the core.
		// Otherwise the altcore is used
		$output = null;
		
		if (is_array($con) )
		{
			if (isset($con['tf']) )
			{
				if ($con['tf'] == 1)
				{
$output = <<<OUTPUT
{$this->fore}{$this->core}{$this->aft}
OUTPUT;
					}
				else
				{
$output = <<<OUTPUT
{$this->fore}{$this->altcore}{$this->aft}
OUTPUT;
					}
				}
			}
			
		return $output;
		}
	}	# End of string_obj class

// ____________________________________________________________________________________________________
class null_obj extends generic_obj
{
	function __construct($con=array() )
	{
		$this->common($con);
		$this->output = $this->local_gen();
		}	# End of construct function

	public function local_gen()
	{
		// This allows us to switch between active and passive elements with just a change in the tag, ignoring all other active settings.
		// This turns an object "off"

		# We need to make sure $this->fore does not end with a ';'
		$fore = $this->fore;
		
		if (!empty($fore) && $fore[-1] !== ';') {$f = $fore . "\n";} else {$f = $fore;}

		return "$f{$this->aft}";
		}
	}	# End of null_obj class

// ____________________________________________________________________________________________________
class killed_obj extends generic_obj
{
	function __construct($con=array() )
	{
		return null;
		}	# End of construct function
	}	# End of killed_obj class

// ____________________________________________________________________________________________________
class comment_obj extends html_common
{
	public $open_tag = "<!-- ";
	public $close_tag = " -->";

	function __construct($con=array() )
	{
		$this->common($con);
		$this->output = $this->gen();
		}	# End of construct function

	public function gen()
	{
		# We need to make sure $this->fore does not end with a ';'
		$fore = $this->fore;
		
		if (!empty($fore) && $fore[-1] !== ';') {$f = $fore . "\n" . $this->open_tag;} else {$f = $fore . $this->open_tag;}
		
		return "$f{$this->core}{$this->close_tag}\n{$this->aft}";
		}
	}	# End of comment obj class

// ____________________________________________________________________________________________________
//											Useful functions
// ____________________________________________________________________________________________________
function setup_con($instr, $alt=";\n")
{
	$instr = str_replace(array('&', "__r__$alt"), array('__and__', $alt), $instr);
	
	parse_str(str_replace($alt, '&', $instr), $con);

	foreach ($con as $key => $val)
	{
		if (strpos($con[$key],  '__r__') !== false) {$con[$key] = explode('__r__', $val);}
		}

	return $con;
	}	# End setup_con function
// ____________________________________________________________________________________________________
function setup_con_with_remainders($str1=null, $str2=null)
{
	$con = setup_con($str1);
	if (!empty($str2) ) {$con['uncommon'] = setup_con($str2);}

	return $con;
	}	# End setup_con function
// ____________________________________________________________________________________________________
function gen_obj_output($type='generic', $con=array() )
{
	$output = null;
	$type = $type . '_obj';

	$obj = (class_exists($type) ) ? new $type($con) : null;
	
	if ($obj !== null && is_object($obj) )
		{$output = str_replace('__and__', '&', $obj->output);}
	
	unset($obj);

	return $output;
	}	# End of gen_obj_output function

// ____________________________________________________________________________________________________
function gen_obj_array($obj_array=array(), $type=null)
{
	$output = '';

	if (empty($type) )
	{
		foreach ($obj_array as $key=>$val)
		{
			$output .= gen_obj_output($val[0], $val[1]);
			}
		}
	else
	{
		foreach ($obj_array as $key=>$val)
		{
			$output .= gen_obj_output($type, $val);
			}
		}

	return $output;
	}	# End of gen_obj_array
// ____________________________________________________________________________________________________
function automatic_object_output($type=null, $innerstr=null, $alt=";\n")
{
	if (empty($type) ) {$type = 'generic';}

	return gen_obj_output($type, setup_con($innerstr, $alt) );
	}	# End of automatic_object_output
// ____________________________________________________________________________________________________
function automatic_obj_array($type=null, $obj_array=array(), $alt=";\n")
{
	$output = '';

	if (empty($type) )
	{
		foreach ($obj_array as $key=>$val)
			{$output .= gen_obj_output($val[0], setup_con($val[1], $alt) );}
		}
	else
	{
		foreach ($obj_array as $key=>$val)
			{$output .= gen_obj_output($type, setup_con($val, $alt) );}
		}

	return $output;
	}	# End of automatic_obj_array
// ____________________________________________________________________________________________________
function handle_e_string($str=null, $split='||', $alt=";\n")
{
	if (empty($alt) ) {$alt = ";\n";}
	if (empty($split) ) {$split = ";\n";}
	
	$a = (!empty($str) ) ? explode($split, $str, 2) : array();
	$innerstr = null;
	
	if (count($a) > 1 && empty($a[0]) ) 
		{$type = 'generic'; $innerstr = $a[1];}	
	elseif (count($a) == 1) 
		{$type = $a[0];}
	elseif (empty($a) )
		{$type = 'generic';}
	else 
		{$type = $a[0]; $innerstr = $a[1];}
	
	return array('type'=>$type, 'innerstr'=>$innerstr, 'alt'=>$alt);
	}
	
// ____________________________________________________________________________________________________
function exploded_object_output($str=null, $split='||', $alt=';\n')
{
	if (empty($str) || empty($split) ) {return null;}
	
	extract(handle_e_string($str, $split, $alt) );

	return gen_obj_output($type, setup_con($innerstr, $alt) );
	}	# End of exploded_object_output
// ____________________________________________________________________________________________________
function exploded_printed_object_output($str=null, $arr=null, $split='||', $alt=';\n')
{
	if (empty($str) || empty($split) ) {return null;}
	
	extract(handle_e_string($str, $split, $alt) );

	return gen_obj_output($type, setup_con(sprintf($innerstr, $arr), $alt) );
	}	# End of exploded_printed_object_output
// ____________________________________________________________________________________________________
function exploded_str_rep_object_output($str=null, $replace=null, $split='||', $alt=';\n')
{
	if (empty($str) || empty($split) ) {return null;}
	
	extract(handle_e_string($str, $split, $alt) );

	if (is_array($replace) ) {$is = str_replace(array_keys($replace), array_values($replace), $innerstr);}
	else {$is = $innerstr;}
	
	return gen_obj_output($type, setup_con($is, $alt) );
	}	# End of exploded_str_rep_object_output
// ____________________________________________________________________________________________________
function exploded_str_rep_get_type_and_con($str=null, $just_type=false, $replace=null, $split='||', $alt=';\n')
{
	if (empty($str) || empty($split) ) {return null;}
	
	$just_type = force_boolean($just_type, false);
	
	extract(handle_e_string($str, $split, $alt) );

	if ($just_type) {return $type;}

	if (is_array($replace) ) {$is = str_replace(array_keys($replace), array_values($replace), $innerstr);}
	else {$is = $innerstr;}
	
	return array('type'=>$type, 'con'=>setup_con($is, $alt) );
	}	# End of exploded_str_rep_get_type_and_con
// ____________________________________________________________________________________________________
function ecore_object_output($str=null)
{
	// Here we explicitly set the first character as the object field value delimiter unless a ';', 
	// in which case we use the first 3 characters, which is safer and backwards compatible.
	// Double that value is the object delimiter, as in {$obj1}delim{$obj2}delim{$obj3}.
	// Do not double the first character at the beginning of the string, only between objects.
	// And the first delimited value as the type for sending to eoo.
	// We assume the single character delimiter is something unusual in unicode.
	// The ecore and/or altecore of an object is sent to this function call.
	// This means that you do not have to quote the ecore value or call a first order function.
	// To parse this iteratively, each ecore and/or altecore level must use a different delimiter.
	if (!empty($str) )
	{
		$split = mb_substr($str, 0, 1, "UTF-8");
		$rem = mb_substr($str, 1, null, "UTF-8");
		
		if ($split === ';')
		{
			$split = mb_substr($str, 0, 3, "UTF-8");
			$rem = mb_substr($str, 3, null, "UTF-8");
			}
		
		if (!empty($rem) )
		{
			$objs = explode("$split$split", $rem);
			
			$output = null;
			
			foreach ($objs as $obj)
				{$output .= exploded_object_output($obj, $split, $split);}
				
			return $output;
			}
		}
	return null;
	}
	
// ____________________________________________________________________________________________________
//											Misc. Functions
// ____________________________________________________________________________________________________
function prepstr($input=null, $length=null)
{
	if ($length !== NULL && is_numeric($length) && $length > 0)
		{return htmlentities(substr(trim($input), 0, $length), ENT_QUOTES | ENT_HTML5, "UTF-8", false);}
	else
		{return htmlentities(trim($input), ENT_QUOTES | ENT_HTML5, "UTF-8", false);}
	}

// ____________________________________________________________________________________________________
function prepstr2($input=null, $length=null)
{
	if ($length !== NULL && is_numeric($length) && $length > 0)
		{return htmlspecialchars(substr(trim($input), 0, $length), ENT_QUOTES | ENT_HTML5, "UTF-8", false);}
	else
		{return htmlspecialchars(trim($input), ENT_QUOTES | ENT_HTML5, "UTF-8", false);}
	}

// ____________________________________________________________________________________________________
function mb_prepstr($input=null, $length=null)
{
	if ($length !== NULL && is_numeric($length) && $length > 0)
		{return htmlentities(mb_substr(trim($input), 0, $length), ENT_QUOTES | ENT_HTML5, "UTF-8", false);}
	else
		{return htmlentities(trim($input), ENT_QUOTES | ENT_HTML5, "UTF-8", false);}
	}

// ____________________________________________________________________________________________________
function mb_prepstr2($input=null, $length=null)
{
	if ($length !== NULL && is_numeric($length) && $length > 0)
		{return htmlspecialchars(mb_substr(trim($input), 0, $length), ENT_QUOTES | ENT_HTML5, "UTF-8", false);}
	else
		{return htmlspecialchars(trim($input), ENT_QUOTES | ENT_HTML5, "UTF-8", false);}
	}

// ____________________________________________________________________________________________________
function html_entity_decodes($input=null)
{
	return html_entity_decode($input, ENT_NOQUOTES | ENT_HTML5, 'UTF-8');
	}

// ____________________________________________________________________________________________________
function html_entity_decodes_iso($input=null)
{
	// If you need re-encode into UTF-8 From ISO-8859-1
	return mb_convert_encoding(html_entity_decode($input, ENT_NOQUOTES | ENT_HTML5, 'ISO-8859-1'), 'UTF-8', 'ISO-8859-1');
	}

// ____________________________________________________________________________________________________
function strip_html($input=null, $tag_list=null, $start=false)
{
	$start = force_boolean($start, false);
	
	// Swap out the tags in the tag_list array
	if (isset($tag_list) && is_array($tag_list) && count($tag_list) > 0)
	{
		if ($start)
		{
			foreach ($tag_list as $t=>$tag)
			{
				$input = str_ireplace("<{$tag}", "&lt;{$tag}", $input);
				}
			}
		else
		{
			foreach ($tag_list as $t=>$tag)
			{
				$input = str_ireplace(	array("<?", "?>", "/>", "&quot;>", "<{$tag}>", "</{$tag}>", " [", " Array", "  "), 
												array("&lt;?", "?&gt;", "&sol;&gt;", "&quot;&gt;", "&lt;{$tag}&gt;", "&lt;&sol;{$tag}&gt;<br>", "<br>[", "<br>Array", " "), $input);
				}
			}
		}

	return $input;
	}
	
// ____________________________________________________________________________________________________
function del_html($input=null, $tag_list=null)
{
	// Remove the tags in the tag_list array
	if (isset($tag_list) && is_array($tag_list) && count($tag_list) > 0)
	{
		foreach ($tag_list as $t=>$tag)
		{
			$input = str_ireplace(array("\n", "&NewL", "<{$tag}>", "</{$tag}>"), array_fill(0, 4, ""), $input);
			}
		}

	return $input;
	}
	
// ____________________________________________________________________________________________________
function html_table_row_swap($input=null)
{
	// Replace table rows with <div> and <span> tags
	$input = str_ireplace(array("<tr>", "</tr>"), array("<div style=\"display:table-row;\">", "</div>\n"), $input);

	$input = str_ireplace(array("<th>", "</th>"), array("<span style=\"display:table-cell;\">", "</span>"), $input);

	$input = str_ireplace(array("<td>", "</td>"), array("<span style=\"display:table-cell;\">", "</span>"), $input);
	
	if (substr($input, -7) == "</span>") {$input .= "</div>";}
	elseif (substr($input, -8) == "</span>&") {$input .= "</div>";}
	
	return $input;
	}
	
// ____________________________________________________________________________________________________
// From Mark-Jason Dominus and Edsger Dijkstra
function pc_next_permutation($p, $size) {
    // slide down the array looking for where we're smaller than the next guy
    for ($i = $size - 1; $p[$i] >= $p[$i+1]; --$i) { }

    // if this doesn't occur, we've finished our permutations
    // the array is reversed: (1, 2, 3, 4) => (4, 3, 2, 1)
    if ($i == -1) { return false; }

    // slide down the array looking for a bigger number than what we found before
    for ($j = $size; $p[$j] <= $p[$i]; --$j) { }

    // swap them
    $tmp = $p[$i]; $p[$i] = $p[$j]; $p[$j] = $tmp;

    // now reverse the elements in between by swapping the ends
    for (++$i, $j = $size; $i < $j; ++$i, --$j) {
         $tmp = $p[$i]; $p[$i] = $p[$j]; $p[$j] = $tmp;
    }

    return $p;
}

// ____________________________________________________________________________________________________
// Borrowed from Symphony/polyfill-php72
/*
 * Copyright (c) 2015-2019 Fabien Potencier

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is furnished
to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
// This is a work-around for the deprecated utf8_decode function
// Convert utf8 to iso8859_1
function utf8_decode_new(string $string): string 
{
	$s = (string) $string;
	$len = strlen($s);

	for ($i = 0, $j = 0; $i < $len; ++$i, ++$j) 
	{
		switch ($s[$i] & "\xF0") 
		{
			case "\xC0":
			case "\xD0":
			$c = (ord($s[$i] & "\x1F") << 6) | ord($s[++$i] & "\x3F");
			$s[$j] = $c < 256 ? \chr($c) : '?';
			break;

			case "\xF0":
			++$i;
			// no break

			case "\xE0":
			$s[$j] = '?';
			$i += 2;
			break;

			default:
			$s[$j] = $s[$i];
			}
		}

	return substr($s, 0, $j);
	}

// ____________________________________________________________________________________________________
// Borrowed from Symphony/polyfill-php72
/*
 * Copyright (c) 2015-2019 Fabien Potencier

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is furnished
to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/
// This is a work-around for the deprecated utf8_encode function
// Convert iso8859_1 to utf8
function utf8_encode_new(string $s): string 
{
	$s .= $s;
	$len = strlen($s);

	for ($i = $len >> 1, $j = 0; $i < $len; ++$i, ++$j) 
	{
		switch (true) 
		{
			case $s[$i] < "\x80": $s[$j] = $s[$i]; break;
			case $s[$i] < "\xC0": $s[$j] = "\xC2"; $s[++$j] = $s[$i]; break;
			default: $s[$j] = "\xC3"; $s[++$j] = chr(ord($s[$i]) - 64); break;
			}
		}

	return substr($s, 0, $j);
	}

// ____________________________________________________________________________________________________
function keyword_regex($input)
{
	// Create a regex for an input string of keywords.
	// Replace all the white space with __r__ 's
	$str1 = preg_replace("/[\s]+/", "__r__", trim($input) );

	// Eliminate all non 'word' characters
	$str2 = preg_replace("/[\W]/", "", $str1);

	// Create a cleaned up array of all keywords
	$out = array();
	$out = explode('__r__', $str2);

	$out = array_unique($out);

	// Eliminate small words and 'the'
	$newout = array();

	foreach ($out as $val)
	{
		if (strlen($val) > 2 && $val != 'the') {$newout[] = $val;}
		}

	$count = count($newout) - 1;
	$perm = range(0, $count);
	$perms = array();
	$j = 0;

	$regex = "(";

	// Generate the core of the regex string, which simply allows all keyword permutations in any order
	// While not perfect, it will work in most cases without worrying about regex versions
	if ($count > 0)
	{
		do 
		{
			foreach ($perm as $i)
				{$perms[$j][] = $newout[$i];}
			}
		while ($perm = pc_next_permutation($perm, $count) and ++$j);
		}
	else
		{$perms[] = $newout;}

	foreach ($perms as $newlist)
	{
		foreach ($newlist as $word)
		{
			$regex .= '.*[[:<:]]' . $word . '[[:>:]].*|';
			}
		$regex = substr($regex, 0 , -1);
		$regex .= ")(";
		}

	$regex = substr($regex, 0, -1);

	return $regex;
	}

// ____________________________________________________________________________________________________
function print_out_array($var)
{
	echo "<pre>\n";
	print_r($var);
	echo "</pre><br>\n";
	}	# End of print out array function

// ____________________________________________________________________________________________________
function string_out_array($var)
{
	return "<pre>\n" . print_r($var, true) . "</pre><br>\n";
	}	# End of print out array function

// ____________________________________________________________________________________________________
function check_index($val=null, $var=true)
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
function check_indexes($vals=array(), $var=true)
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
function force_boolean($var, $def=true)
{
	if ($def != true && $def != false) {$def = true;}
	if ($var != true && $var != false) {return $def;} else {return $var;}
	}	# End of print out array function

// ____________________________________________________________________________________________________
function convert_newline_to_br_tag($input=null)
{
	return str_replace(array("\n", "\n\r", "\r", "\r\n"), array("<br>"), $input);
	}

// ____________________________________________________________________________________________________
function convert_br_tag_to_newline($input=null)
{
	return str_replace(array("<br>", "<br />", "<br/>"), array("\n"), $input);
	}

// ____________________________________________________________________________________________________
function check_offset($a=array(), $o=0)
{
	if (!is_array($a) ) {return null;}
	if (!check_index($o) && $o !== 0) {return null;}

	if (isset($a[$o]) ) {return $a[$o];} else {return null;}
	}

// ____________________________________________________________________________________________________
function check_key($a=array(), $k=null)
{
	if (!is_array($a) ) {return null;}
	if (empty($k) ) {return null;}

	if (array_key_exists($k, $a) ) {return $a[$k];} else {return null;}
	}

// ____________________________________________________________________________________________________
///											Useful First Order Variables
// ____________________________________________________________________________________________________
	$goo = 'gen_obj_output';
	$goa = 'gen_obj_array';
	$sc = 'setup_con';
	$scwr = 'setup_con_with_remainders';
	$aoo = 'automatic_object_output';
	$aoa = 'automatic_obj_array';
	$eoo = 'exploded_object_output';
	$epoo = 'exploded_printed_object_output';
	$esroo = 'exploded_str_rep_object_output';
	$esrgtac = 'exploded_str_rep_get_type_and_con';
	$ecoo = 'ecore_object_output';
	$ps = 'prepstr';
	$ps2 = 'prepstr2';
	$mbps = 'mb_prepstr';
	$mbps2 = 'mb_prepstr2';
	$kr = 'keyword_regex';
	$poa = 'print_out_array';
	$soa = 'string_out_array';
	$hed = 'html_entity_decodes';
	$hedi = 'html_entity_decodes_iso';
	$cinx = 'check_index';
	$cinxes = 'check_indexes';
	$fb = 'force_boolean';
	$cnltobr = 'convert_newline_to_br_tag';
	$cbrtonl = 'convert_br_tag_to_newline';
	$coff = 'check_offset';
	$ckkey = 'check_key';
// ____________________________________________________________________________________________________

?>
