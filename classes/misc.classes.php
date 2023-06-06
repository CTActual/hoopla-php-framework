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

// Menu maker function, which depends on the html library

function menu_maker($list_array=null, $newpg=1)
{
	global $aoo, $aoa;

	if (!is_array($list_array) ) {return null;}

	$menu = array();

	foreach ($list_array as $index=>$value)
	{
		if (!empty($value[0]) )
		{
			$menu[] = "core={$aoo('link', "class=menuclass;\nhref={$value[0]};\ncore={$value[1]};\nnewpg={$newpg}")}";
			}
		else
			{$menu[] = "core=&nbsp;";}
		}

	return $aoo('ul', "core={$aoa('li', $menu)}");
	}

//"___________________________________________________________________________________________

function row_color($i=0, $even="#FFF", $odd="#CFC")
{
	$s = "background-color:";
	
	if ( ($i % 2) == 0) 
		{return (empty($even) ) ? "{$s}#FFF;" : "{$s}$even;";}
	else
		{return (empty($odd) ) ? "{$s}#CFC;" : "{$s}$odd;";}
	}

//___________________________________________________________________________________________


$mm = 'menu_maker';


?>
