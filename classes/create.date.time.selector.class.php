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

// This creates the HTML drop downs to allow users to select dates/times

class date_selector
{
	public $selstr;				# the return value

	function __construct(	$mon_obj_name = null, 
									$day_obj_name = null, 
									$yr_obj_name = null, 
									$time_obj_name = null, 
									$mon_sel_val = null, 
									$day_sel_val = null, 
									$yr_sel_val = null, 
									$time_sel_val = null, 
									$hr_sel_offset = null, 
									$min_sel_offset = null, 
									$yrs_total=null, 
									$yrs_future=1)
	{
		$goo = 'gen_obj_output';
		$goa = 'gen_obj_array';
		$sc = 'setup_con';
		$ps = 'prepstr';
		$kr = 'keyword_regex';
		$poa = 'print_out_array';
		$hed = 'html_entity_decodes';

		// Need to set the number of years that will be displayed
		if (is_null($yrs_total) || !is_numeric($yrs_total) || $yrs_total < 1) 
			{$yrs_total = 3;}
		elseif ($yrs_total > 50)
			{$yrs_total = 50;}
		else
			{$yrs_total = (int) floor($yrs_total);}

		// The hour and minute offsets are determined by the call to the function, but these can bump the day, month and year around
		$mon_offset = 0;
		$day_offset = 0;
		$yr_offset = 0;

		// The minute offset can bump the hour around
		if ((15*round((date('i') + $min_sel_offset)/15)) > 59)
			{$hr_sel_offset = $hr_sel_offset + 1; $min_sel_offset = $min_sel_offset - 60;}
		elseif ((15*round((date('i') + $min_sel_offset)/15)) < 0)
			{$hr_sel_offset = $hr_sel_offset - 1; $min_sel_offset = $min_sel_offset + 60;}

		// The hour offset can bump the day around
		if ((date('G') + $hr_sel_offset) > 23) 
			{$day_offset = 1; $hr_sel_offset = $hr_sel_offset - 24;}
		elseif ((date('G') + $hr_sel_offset) < 0) 
			{$day_offset = -1; $hr_sel_offset = $hr_sel_offset + 24;}

		$cur_mon = (int) date('n');
		$cur_day = (int) date('j');
		$cur_year = (int) date('Y');

		// The day offset can bump the month around and the year by the month
		if ($day_offset == 1)
		{
			if (in_array($cur_mon, array(1, 3, 5, 7, 8, 10, 12) ) )
			{
				if ($cur_mon != 12 && $cur_day == 31) 
					{$mon_offset = 1; $day_offset == -30;}
				elseif ($cur_mon == 12 && $cur_day == 31) 
					{$mon_offset = -11; $day_offset == -30; $yr_offset = 1;}
				}
			elseif (in_array($cur_mon, array(4, 6, 9, 11) ) )
			{
				if ($cur_day == 30) 
					{$mon_offset = 1; $day_offset == -29;}
				}
			elseif ($cur_mon == 2 && ($cur_year % 4) != 0 && $cur_day == 28)
				{$mon_offset = 1; $day_offset == -27;}
			elseif ($cur_mon == 2 && ($cur_year % 4) == 0 && $cur_day == 29)
				{$mon_offset = 1; $day_offset == -28;}
			}
		elseif ($day_offset == -1 && $cur_day == 1)
		{
			$mon_offset = -1;

			if (in_array($cur_mon, array(1, 2, 4, 6, 8, 9, 11) ) )
			{
				$day_offset = 30;

				if($cur_mon == 1)
					{$mon_offset = 11; $yr_offset = -1;}
				}
			elseif (in_array($cur_mon, array(5, 7, 10, 12) ) )
				{$day_offset = 29;	}
			elseif ($cur_mon == 3 && ($cur_year % 4) != 0)
				{$day_offset = 27;}
			elseif ($cur_mon == 3 && ($cur_year % 4) == 0)
				{$day_offset = 28;}
			}

		$output = "";

		// Now that all proper offsets have been determined, we can create the dropdowns

		// Display Months
		if (!empty($mon_obj_name) )
		{
			$output .= "<select name=\"$mon_obj_name\" >\n";

			for ($mon = 1; $mon <= 12; $mon++)
			{
				$emon = str_pad($mon, 2, "0", STR_PAD_LEFT);
				$output .=  "<option value=\"$emon\" ";

				if ($mon_sel_val !== NULL && $mon_sel_val == $emon) 
					{$output .= "selected=\"selected\" ";}
				elseif ($mon_sel_val === NULL && $mon == (int) date('n') + $mon_offset) 
					{$output .= "selected=\"selected\" ";}

				$output .= ">$emon&nbsp;</option>\n";
		      		}
			$output .= "</select>\n";
			}


		// Display Days
		if (!empty($day_obj_name) )
		{
			$output .= "<select name=\"$day_obj_name\" >\n";
			for ($day = 1; $day <= 31; $day++)
			{
				$eday = str_pad($day, 2, "0", STR_PAD_LEFT);
				$output .=  "<option value=\"$eday\" ";

				if ($day_sel_val !== NULL && $day_sel_val == $eday) 
					{$output .= "selected=\"selected\" ";}
				elseif ($day_sel_val === NULL && $day == (int) date('j') + $day_offset) 
					{$output .= "selected=\"selected\" ";}

				$output .= ">$eday&nbsp;</option>\n";
		      		}
			$output .= "</select>\n";
			}


		// Display Years
		if (!is_null($yr_obj_name) && $yr_obj_name != "")
		{
			$output .= "<select name=\"$yr_obj_name\" >\n";
			for ($eyear = date('Y') - $yrs_total; $eyear <= date('Y') + $yrs_future; $eyear++)
			{
				$output .=  "<option value=\"$eyear\" ";

				if ($yr_sel_val !== NULL && $yr_sel_val == $eyear) 
					{$output .= "selected=\"selected\" ";}
				elseif ($yr_sel_val === NULL && $eyear == date('Y') + $yr_offset) 
					{$output .= "selected=\"selected\" ";}

				$output .= ">$eyear&nbsp;</option>\n";
		      		}
			$output .= "</select>\n";
			}


		// Display Time
		if (!empty($time_obj_name) )
		{
			$output .= "<select name=\"$time_obj_name\" >\n";
			for ($hr = 0; $hr <= 23; $hr++)
			{
				for ($min = 0; $min <= 59; $min += 15)
				{
					$etime = str_pad($hr, 2, "0", STR_PAD_LEFT) . ":" . str_pad($min, 2, "0", STR_PAD_LEFT) . ":00";
					$output .=  "<option value=\"$etime\" ";

					if (!empty($time_sel_val) && $time_sel_val == $etime) 
						{$output .= "selected=\"selected\" ";}
						elseif (	empty($time_sel_val) && 
								$hr_sel_offset !== NULL &&  
								$hr == (int) date('G') + $hr_sel_offset && 
								$min == (15*round( (date('i') + $min_sel_offset)/15) ) ) 
						{$output .= "selected=\"selected\" ";}

					$output .= ">$etime&nbsp;</option>\n";
			      		}
				}
			$output .= "</select>\n";
			}

		$this->selstr = $output;
		return null;
		} # End of construct function
	} # End of class

//_________________________________________________________________________________________________________
function gen_date_selector(	$mon_obj_name=null, 
						$day_obj_name=null, 
						$yr_obj_name=null, 
						$time_obj_name=null, 
						$mon_sel_val=null, 
						$day_sel_val=null, 
						$yr_sel_val=null, 
						$time_sel_val=null, 
						$hr_sel_offset=null, 
						$min_sel_offset=null, 
						$yrs_total=null, 
						$yrs_future=1)
{
	$date_obj = new date_selector(	$mon_obj_name, 
								$day_obj_name, 
								$yr_obj_name, 
								$time_obj_name, 
								$mon_sel_val, 
								$day_sel_val, 
								$yr_sel_val, 
								$time_sel_val, 
								$hr_sel_offset, 
								$min_sel_offset, 
								$yrs_total, 
								$yrs_future);

	$date_str = $date_obj->selstr;

	unset($date_obj);

	return $date_str;
	}

//_________________________________________________________________________________________________________
function check_calendar_dates($year, $mon, $day=32)
{
	// Make sure the date selection is possible for the given month and year
	// Will return the last day of the month if $day is sent as a null value
	// Returns a string of always two digits for date formatting correctness
	$year = floor($year);
	$mon = floor($mon); 
	$day = floor($day); 

	if ($mon < 1 || $mon > 12) {return null;}

	if ($day < 1) {return "01";}

	if ($day < 29) {return str_pad($day, 2, "0", STR_PAD_LEFT);}

	if ($mon != 2 && $day < 31) {return str_pad($day, 2, "0", STR_PAD_LEFT);}

	if (in_array($mon, array(1, 3, 5, 7, 8, 10, 12) ) && $day < 32) {return str_pad($day, 2, "0", STR_PAD_LEFT);} 

	if (in_array($mon, array(4, 6, 9, 11) ) && $day > 30) {return "30";}
	
	if (in_array($mon, array(1, 3, 5, 7, 8, 10, 12) ) && $day > 31) {return "31";} 

	if ($mon == 2 && ($year % 4) != 0 && $day > 28) {return "28";}

	if ($mon == 2 && ($year % 4) == 0 && $day > 29) {return "29";} else {return $day;}
	}

//_________________________________________________________________________________________________________
function check_this_date($datepassed)
{
	$datepassed = str_replace('T', ' ', $datepassed);

	 return preg_match("/([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})/i", $datepassed);
	}

//_________________________________________________________________________________________________________
function gen_flat_number_array($begin=1, $end=100, $len=2, $interval=1, $str='__r__')
{
	if (!is_numeric($begin) ) {return null;}
	if (!is_numeric($end) ) {return null;}
	if (!is_numeric($len) ) {$len = 2;}
	if (!is_numeric($interval) ) {$interval = 1;}

	$result = range($begin, $end, $interval);
	$code = "";

	foreach($result as $val)
		{$code .= str_pad($val, $len, "0", STR_PAD_LEFT) . $str;}

	$cut = -1*strlen($str);

	return substr($code, 0, $cut);
	}

//_________________________________________________________________________________________________________

$gds = 'gen_date_selector';
$ccd = 'check_calendar_dates';
$gfna = 'gen_flat_number_array';
$ctd = 'check_this_date';

?>
