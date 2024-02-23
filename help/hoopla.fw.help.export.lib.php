<?php
/*
Copyright 2009-2024 Cargotrader, Inc. All rights reserved.

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

require_once('hoopla.fw.rel.path.php');

require_once($incpath . 'common.incs.php');

?>

<!DOCTYPE html> 
<html>

<?php
	$title = "Hoopla Framework";
	include($incpath . "std.header.php");
?>

<body>
  <div id="main">		

    <header>

<?php
	include($incpath . "std.nav.php");
	$banner = "The Hoopla PHP Framework";
	$sub_banner = "Exported Library";
	include($incpath . "std.banner.php");
?>
    </header>
    
	<div id="site_content">		
	
<?php
	include($sidepath . "std.sidebar.top.php");
	include($sidepath . "std.sidebar.bot.php");
?>

	  <div id="content">
        <div class="content_item">
		  <h1>Getting Output From the Export Library</h1> 

		<p>The HFW Export Library serves two main purposes: &nbsp;to allow access to the exported HFW database on the production server (or any server, really), and to allow templates to get object values.</p>

		<p>We shall cover the library starting with the files included and then moving on in detail with what is in them.</p>

		<h1>The Library Files</h1>

		<p>The following files come with the library (from the folder <i>hfw.export.lib</i>):</p>

		<ol>
			<li><i>hfw.db.info.php</i> is the database connection string information of your local installation. Please set up as needed.  Don't copy to your production server.</li>
			<li><i>hfw.db.info.php.srv</i> is the database connection string information of your production installation. Please set up as needed.  Copy to your production server and rename it <i>hfw.db.info.php</i></li>
			<li><i>hfw.export.lib.php</i> has the functions for populating templates, and the main focus for this help page.</li>
			<li><i>hfw.mysqli.class.php</i> contains the classes and functions that the <i>hfw.export.lib.php</i> library needs to read the database and should be transparent to the end user.</li>
		</ol>

		<p>You will need to include or require both <i>hfw.export.lib.php</i> and <i>hfw.mysqli.class.php</i> on every project template page.  As in the PHP command <i>include</i> or <i>require</i>, not code copied directly.</p>
		<p>You will probably want to rename the connection string files depending on the server they are installed on so that the appropriate file is named <i>hfw.db.info.php</i>, which is what <i>hfw.mysqli.class.php</i> looks for.</p>
		<p>In addition to these four exported files, you will need an exported copy of your HFW database as an sql import statement and a HFW database user creation script with the same information that goes into <i>hfw.db.info.php</i>.</p>
		<p>The original versions of these files are in the folder <i>hfw.scripts</i> as <i>create.hfw.db.sql</i> and <i>create.hfw.usrs.sql</i>; but do not install a blank version of the database on your production server!</p>
		<p>Do not make the export folder or its files world accessible on the production server.  These are not files that should be visible to clients.  Import the whole folder by itself on the production server, as it is in the project file structure.</p>

		<h1><i>hfw.db.info.php</i> and <i>hfw.db.info.php.srv</i></h1>

		<p>These are very simple PHP pages that get imported into <i>hfw.mysqli.class.php</i> depending on the context.  Only <i>hfw.db.info.php</i> is actually referenced in <i>hfw.mysqli.class.php</i>, so whichever file has that name will be used.</p>
		<p>You may wish to only upload the file in need on the production server and ignore the file for local connections to add to security, particularly since the file naming will get confusing otherwise.</p>
		<p>If you are familiar with MySQL connection strings, then these files should be self-explanatory.  However, the main feature is to access user accounts only on a per-need basis.  To that end there are four potential users referenced.</p>
		<p>The four users are the default user, the user for select statements, the user for add&sol;update statements and the user for delete statements.</p>
		<p>The four users can have whatever username and password combo you like, but they will need to match the user creation information from <i>create.hfw.usrs.sql</i>.  The local and production servers should not match.</p>
		<p>The four users should be distinct and have long passwords that are not easily guessed.  The default users will give you some idea of what to set up.</p>
		<p>Additionally, you will need to make sure that the database name matches the one for the context (local or production), and the one from <i>create.hfw.usrs.sql</i>.  All these files should be in agreement, though the database name locally should not match the one on the production server.</p>
		<p>You may need to tweak the remaining settings depending on your hosting environment.  Please reference MySQL help on connection strings for more information.</p>

		<h1><i>hfw.mysqli.class.php</i></h1>

		<p>This file, while pivotal, should be transparent to you, and therefore not something that needs much explanation.  Be aware that <i>hfw.export.lib.php</i> depends on it to connect to the HFW database.</p>

		<h1><i>hfw.export.lib.php</i></h1>

		<p>This the main file in the export.  You, the project creator, will need to learn how to use the main functions that come from this file.</p>

<!-- hfw_return_value -->
		<h3>&starf;hfw_return_value</h3>
		
		<p>In almost every case, the function of interest will be <i>hfw_return_value</i>, or its first class alias <i>$hfwrv</i>.  The general help for that follows:</p>

		<ul>
			<li>INPUT: (<b>$pagenum</b>=null, <b>$location</b>=null, <b>$ctx</b>=1, <b>$def</b>=false, <b>$type</b>=null, <b>$named_ctx</b>=null, <b>$named_type</b>=null)</li>
			<li class="li_inner"><b>$pagenum</b>	<i>required</i> (either the page id itself&#8212;an integer&#8212;or the page name alias&#8212;as text)</li>
			<li class="li_inner"><b>$location</b>	<i>required</i> (always the location text&#8212;an alias for the object to be called)</li>
			<li class="li_inner"><b>$ctx</b>			<i>not required</i>, <i><b>default value=1</b></i> (this is always the context id integer, see <b>$named_ctx</b>)</li>
			<li class="li_inner"><b>$def</b>			<i>not required</i>, <i><b>default value=false</b></i> (this is either true or false whether to force the use of the default value or not)</li>
			<li class="li_inner"><b>$type</b>			<i>not required</i>, <i><b>default value=null</b></i> (this is always the setting type id integer, see <b>$named_type</b>, but only use if there is more than one setting type for an object)</li>
			<li class="li_inner"><b>$named_ctx</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is the text alias name for a context, though the default context is assumed so only use if another context is needed)</li>
			<li class="li_inner"><b>$named_type</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is the text setting type name alias to be used instead of the id integer&#8212;see <b>$type</b>&#8212;and only if more than one setting type exists for an object)</li>
			<li class="li_inner">&nbsp;</li>
			<li>Typical INPUT example: <b>$hfwrv('index', 'loc1')</b>, where <i>index</i> is the URL Tag of the page desired and <i>loc1</i> is the Location tag of the object assignment on the page <i>index</i>.</li>
			<li>Calling a named context: <b>$hfwrv('index', 'loc1', null, null, null, 'other')</b>, where <i>other</i> is the non-default context.  The default context is <i>default</i>, which is assumed if no other context is used.</li>
			<li>Calling a default value: <b>$hfwrv('index', 'loc1', null, true)</b>, where <i>true</i> denotes that the default value&#8212;if it exists&#8212;shall not be overridden by the page specific object value.</li>
			<li class="li_inner">&nbsp;</li>
			<li>OUTPUT: Returns the string value of the object in situ, an error message, no value as null, or multiple values in an array if not enough information was given.</li>
			<li class="li_inner">If an array is returned you will likely get an error in your template PHP unless you put in handling for it, but more than likely fixing the call will be better.</li>
			<li class="li_inner">The output array is <b>array[0 ... N]['val'=><i>the in situ value</i>, 'type'=><i>the setting type id</i>, 'pg'=><i>the page id</i>, 'ctx_id'=><i>the context id</i>]</b></li>
			<li class="li_inner">Only one context can be returned since a context is required, even if only the default context.</li>
			<li class="li_inner">The page value generally overrides the default value if it exists, unless the default value is specifically requested with $def.</li>
			<li class="li_inner">While text values are easier to deal with on a template, there is no checking to see if they make any sense.  If not, no value is returned.  For example, if there is no page with the URL Tag <i>index</i> then no value will be returned.  The function does not return an error warning that there is no page with that tag.</li>
			<li class="li_inner">The output logic for this call was revised in 2024 to favor specific calls over default values and make use of the new <b>use_def_ctx_bit</b> field in <b>pg_pg_obj_brg</b>.  This may change output behavior and require some refactoring of older code.</li>
		</ul>
		
		<h3>&starf;hfwn_return_value</h3>
		
		<p>A similar function, <i>hfwn_return_value</i>, or its first class alias <i>$hfwnrv</i>&#8212;note the use of <b>n</b> in both names&#8212;can be used when text entries are desired.  The general help for that follows:</p>

		<ul>
			<li>INPUT: (<b>$pagenum</b>=null, <b>$location</b>=null, <b>$named_ctx</b>=null, <b>$named_type</b>=null, <b>$def</b>=false)</li>
			<li class="li_inner"><b>$pagenum</b>	<i>required</i> (either the page id itself&#8212;an integer&#8212;or the page name alias&#8212;as text)</li>
			<li class="li_inner"><b>$location</b>	<i>required</i> (always the location text&#8212;an alias for the object to be called)</li>
			<li class="li_inner"><b>$named_ctx</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is the text alias name for a context, though the default context is assumed so only use if another context is needed)</li>
			<li class="li_inner"><b>$named_type</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is the text setting type name, but only if more than one setting type exists for an object)</li>
			<li class="li_inner"><b>$def</b>			<i>not required</i>, <i><b>default value=false</b></i> (this is either true or false whether to force the use of the default value or not)</li>
			<li class="li_inner">&nbsp;</li>
			<li>Typical INPUT example: <b>$hfwnrv('index', 'loc1')</b>, where <i>index</i> is the URL Tag of the page desired and <i>loc1</i> is the Location tag of the object assignment on the page <i>index</i>.</li>
			<li>Calling a named context: <b>$hfwnrv('index', 'loc1', 'other')</b>, where <i>other</i> is the non-default context.  The default context is <i>default</i>, which is assumed if no other context is used.</li>
			<li>Calling a default value: <b>$hfwnrv('index', 'loc1', null, null, true)</b>, where <i>true</i> denotes that the default value&#8212;if it exists&#8212;shall not be overridden by the page specific object value.</li>
			<li class="li_inner">&nbsp;</li>
			<li>OUTPUT: See the OUTPUT for <i>$hfwrv</i> above</li>
			<li>This is a very similar function call to <i>$hfwrv</i>, but helpful when dealing with text aliases instead of either text aliases or ids.</li>
		</ul>
		
		<h3>&starf;hfwn_csv_return_value</h3>
		
		<p>A similar function, <i>hfwn_csv_return_value</i>, or its first class alias <i>$csvhfwnrv</i>&#8212;note the use of <b>n</b> in the name&#8212;can be used when text entries are desired.  The general help for that is the same as for $hfwnrv, except the parameters are a csv string in the same order.  Note that you cannot pass variables in a fixed string, it must be interpreted first.</p>

		<ul>
			<li>INPUT: (<b>$csv</b>=null, <b>$alt</b>=&apos;,&apos;, <b>$new_set_type</b>=null)</li>
			<li class="li_inner"><b>$csv</b>	<i>required</i> (CSV string of $hfwnrv input params.  The inputs for $hfwnrv are ($pagenum=null, $location=null, $named_ctx=null, $named_type=null, $def=false) )</li>
			<li class="li_inner"><b>$alt</b>	<i>not required</i>, <i><b>default value=&apos;,&apos;</b></i> (string delimiter alternative)</li>
			<li class="li_inner"><b>$new_set_type</b>	<i>not required</i>, <i><b>default value=null</b></i> (change out the csv string namted setting type with this named setting type.)</li>
			<li class="li_inner">&nbsp;</li>
			<li>Typical INPUT example: <b>$csvhfwnrv(&apos;index,loc1,def_ctx,csv&apos;)</b>, call $hfwnrv(&apos;index&apos;, &apos;loc1&apos;, &apos;def_ctx&apos;, &apos;csv&apos;).</li>
			<li>Possible INPUT example: <b>$csvhfwnrv(&apos;index|loc1|def_ctx|csv&apos;, &apos;|&apos;, &apos;txt&apos;)</b>, call $hfwnrv(&apos;index&apos;, &apos;loc1&apos;, &apos;def_ctx&apos;, &apos;txt&apos;), using &apos;|&apos; as the string delimiter instead of &apos;,&apos; and replacing setting type &apos;csv&apos; with &apos;txt&apos;.</li>
			<li class="li_inner">&nbsp;</li>
			<li>OUTPUT: The output is the same as for <i>$hfwrv</i>.</li>
		</ul>
		
		<p><i>hfw_return_value</i>, <i>hfwn_return_value</i> and <i>hfwn_csv_return_value</i> all rely on a simple master PHP class in the library called <i>hoopla_get_obj_val</i>.  This class can be called directly, but it is not recommended.  This would only be done if some low level processing is needed on the template.  The class does provide more detailed output, such as some error messages, and the entire output array.  However, this will probably be of limited help.</p>

<!-- hfw_return_all_vals -->
		<h3>&starf;hfw_return_all_vals</h3>
		
		<p>If you thought that the Hoopla Framework merely helped you organize your website, there is also the powerful feature of page spanning, as exemplified by the function <i>hfw_return_all_vals</i>, or its first class alias <i>$hfwrav</i>.  The general help for that follows:</p>

		<ul>
			<li>INPUT: (<b>$location</b>=null, <b>$ctx</b>=1, <b>$type</b>=null, <b>$named_ctx</b>=null, <b>$named_type</b>=null)</li>
			<li class="li_inner"><b>$location</b>	<i>required</i> (always the location text&#8212;an alias for the object to be called)</li>
			<li class="li_inner"><b>$ctx</b>			<i>not required</i>, <i><b>default value=1</b></i> (this is always the context id integer, see <b>$named_ctx</b>)</li>
			<li class="li_inner"><b>$type</b>			<i>not required</i>, <i><b>default value=null</b></i> (this is always the setting type id integer, see <b>$named_type</b>, but only use if there is more than one setting type for an object)</li>
			<li class="li_inner"><b>$named_ctx</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is the text alias name for a context, though the default context is assumed so only use if another context is needed)</li>
			<li class="li_inner"><b>$named_type</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is the text setting type name alias to be used instead of the id integer&#8212;see <b>$type</b>&#8212;and only important if more than one setting type exists for an object)</li>
			<li class="li_inner">&nbsp;</li>
			<li>Typical INPUT example: <b>$hfwrav(&apos;loc1&apos;)</b>, where <i>loc1</i> is the Location tag of the object assignment on all the pages to be returned.</li>
			<li>Calling a named context: <b>$hfwrav(&apos;loc1&apos;, null, null, &apos;other&apos;)</b>, where <i>other</i> is the non-default context.  The default context is <i>default</i>, which is assumed if no other context is used.</li>
			<li>Forcing a type: <b>$hfwrav(&apos;loc1&apos;, null, 19)</b>, where <i>19</i> forces the search to look for the setting type <i>HTML</i>.</li>
			<li class="li_inner">&nbsp;</li>
			<li>OUTPUT: The output array is <b>array[0 ... N]['val'=><i>the in situ value</i>, 'set_type_id'=><i>the setting type id</i>, 'obj_name'=><i>the object name</i>, 'obj_acs_str'=><i>the object access string</i>, 'pg_id'=><i>the page id</i>, 'set_type_name'=><i>the setting type name</i>, 'set_type_lbl'=><i>the setting type label</i>, 'pg_name'=><i>the page name</i>, 'pg_obj_id'=><i>the page as object id</i>, 'pg_acs_str'=><i>the page access string</i>, 'spc_ord'=><i>the special order from Values-by-Object page settings</i>]</b></li>
			<li class="li_inner">Only one context can be returned since a context is required, even if only the default context.</li>
			<li class="li_inner">Default values are not returned.  Only pages with values are returned.</li>
			<li class="li_inner">More than one set of pages can be returned if there are multiple setting types in use, but the return set provides the setting type information for more filtering.</li>
			<li class="li_inner">The returned set is ordered first by setting type id, then by special order, then by page name.  If the special ordering is not set, then the page name will take over.</li>
			<li class="li_inner">For example, the set of pages with <i>HTML</i> setting types will come first, then the set of pages with <i>Text</i> setting types, etc.  Only if there are values, of course.</li>
			<li class="li_inner">&nbsp;</li>
			<li>There are several ways to set up a page spanning set of object values.  You can create an object, give it a location on the desired page, save values for it on those pages and then use this function to call the result set on every page.  This is little different from creating and using a normal object except for intent.  The location must be the same on all the pages in the set, however.</li>
		</ul>
		
		<h3>&starf;hfwn_return_all_vals</h3>
		
		<p>As before, there is a helper function for <i>$hfwrav</i> called <i>hfwn_return_all_vals</i>, or its first class alias <i>$hfwnrav</i>.  The general help for that follows:</p>

		<ul>
			<li>INPUT: (<b>$location</b>=null, <b>$named_ctx</b>=null, <b>$named_type</b>=null)</li>
			<li class="li_inner"><b>$location</b>	<i>required</i> (always the location text&#8212;an alias for the object to be called)</li>
			<li class="li_inner"><b>$named_ctx</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is the text alias name for a context, though the default context is assumed so only use if another context is needed)</li>
			<li class="li_inner"><b>$named_type</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is the text setting type name alias, but only important if more than one setting type exists for an object)</li>
			<li class="li_inner">&nbsp;</li>
			<li>Typical INPUT example: <b>$hfwnrav('loc1')</b>, where <i>loc1</i> is the Location tag of the object assignment on all the pages to be returned.</li>
			<li>Calling a named context: <b>$hfwnrav('loc1', 'other')</b>, where <i>other</i> is the non-default context.  The default context is <i>default</i>, which is assumed if no other context is used.</li>
			<li>Forcing a type: <b>$hfwrav('loc1', null, 'html')</b>, where <i>html</i> forces the search to look for the setting type <i>HTML</i>.</li>
			<li class="li_inner">&nbsp;</li>
			<li>OUTPUT: The output is the same as for <i>$hfwrav</i>.</li>
		</ul>
		
<!-- hfw_get_ctx_vals -->
		<h3>&starf;hfw_get_ctx_vals</h3>
		
		<p>Page spanning continues with a context oriented function <i>hfw_get_ctx_vals</i>, or its first class alias <i>$hfwgcv</i>.  The general help for that follows:</p>

		<ul>
			<li>INPUT: (<b>$ctx</b>=1, <b>$obj_type</b>=null, <b>$set_type</b>=null, <b>$asc_pg</b>=null, <b>$val_pg</b>=null, <b>$named_ctx</b>=null, <b>$named_obj_type</b>=null, <b>$named_set_type</b>=null, <b>$named_asc_pg</b>=null,  <b>$named_val_pg</b>=null, <b>$get_def_bit</b>=false, <b>$loc_filter</b>=null)</li>
			<li class="li_inner"><b>$ctx</b>	<i>not required</i>, <i><b>default value=1</b></i> (this is always the context id integer&mdash;see <b>$named_ctx</b>&mdash;can be an array of values)</li>
			<li class="li_inner"><b>$obj_type</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is always the object type id integer, see <b>$named_obj_type</b>, use as a filter; either single id or an array)</li>
			<li class="li_inner"><b>$set_type</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is always the setting type id integer, see <b>$named_set_type</b>, use as a filter)</li>
			<li class="li_inner"><b>$asc_pg</b> <i>not required</i>, <i><b>default value=null</b></i> (this is always the pg id integer of the object associated page, see <b>$named_asc_pg</b>, use as a filter)</li>
			<li class="li_inner"><b>$val_pg</b> <i>not required</i>, <i><b>default value=null</b></i> (this is always the pg id integer of the setting value page, see <b>$named_val_pg</b>, use as a filter)</li>
			<li class="li_inner"><b>$named_ctx</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is the text alias name for a context, though the default context is assumed so only use if another context is needed)</li>
			<li class="li_inner"><b>$named_obj_type</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is the object type name alias to be used instead of the id integer&#8212;see <b>$obj_type</b>&#8212;used as a filter; either single string value or array)</li>
			<li class="li_inner"><b>$named_set_type</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is the text setting type name alias to be used instead of the id integer&#8212;see <b>$set_type</b>&#8212;used as a filter)</li>
			<li class="li_inner"><b>$named_asc_pg</b> <i>not required</i>, <i><b>default value=null</b></i> (this is always the pg tag of the object associated page, see <b>$asc_pg</b>, use as a filter)</li>
			<li class="li_inner"><b>$named_val_pg</b> <i>not required</i>, <i><b>default value=null</b></i> (this is always the pg tag of the setting value page, see <b>$val_pg</b>, use as a filter)</li>
			<li class="li_inner"><b>$get_def_bit</b> <i>required</i>, <i><b>default value=false (changed)</b></i> (retreive fallback default values if true for page values or ctx values if they have been set and active for the object.  Will be retrieved in addition to any specific page or ctx values.)</li>
			<li class="li_inner"><b>$loc_filter</b> <i>not required</i>, <i><b>default value=null</b></i> (a single location string or array of location strings to search on for values.)</li>
			<li class="li_inner">&nbsp;</li>
			
			<li>Typical INPUT example, calling a context, object type and setting type by name: <b>$hfwgcv(null, null, null, null, 'ctx1', 'frm_obj', 'html')</b>.</li>
			<li>The same using ids: <b>$hfwgcv(3, 8, 19)</b>.</li>
			<li class="li_inner">&nbsp;</li>
			
			<li>OUTPUT: The output array is <br>&emsp;&emsp;
			<b>array[0 ... N]['set_val_id'=><i>the setting id</i>, <br>&emsp;&emsp;
			'pg_obj_id'=><i>the page object id</i>, <br>&emsp;&emsp;
			'pg_obj_set_type_id'=><i>the page object setting type id</i>, <br>&emsp;&emsp;
			'val'=><i>the setting value</i>, <br>&emsp;&emsp;
			'set_val_pg_id'=><i>the page id for the setting value-if any</i>, <br>&emsp;&emsp;
			'ctx_id'=><i>context id</i>, <br>&emsp;&emsp;
			'ctx_name'=><i>context name</i>, <br>&emsp;&emsp;
			'ctx_lbl'=><i>context label</i>, <br>&emsp;&emsp;
			'ctx_spc_ord'=><i>context special order</i>, <br>&emsp;&emsp;
			'set_type_name'=><i>the type name</i>, <br>&emsp;&emsp;
			'set_type_lbl'=><i>the setting type label</i>, <br>&emsp;&emsp;
			'obj_type_name'=><i>the page object type name</i>, <br>&emsp;&emsp;
			'obj_type_lbl'=><i>the page object type label</i>, <br>&emsp;&emsp;
			'obj_name'=><i>the object label</i>, <br>&emsp;&emsp;
			'asc_pg_id'=><i>the id of the object associated page</i>, <br>&emsp;&emsp;
			'asc_pg_obj_loc'=><i>the label tag of the object associated page</i>,<br>&emsp;&emsp;
			'obj_spec_ord'=><i>object special order</i>, <br>&emsp;&emsp;
			'use_def_bit'=><i>use page default value</i>, <br>&emsp;&emsp;
			'use_def_ctx_bit'=><i>use default context (def_ctx) value]</i></b></li>
			<li class="li_inner">Only one context can be returned since a context is required, even if only the default context.</li>
			<li class="li_inner">Default values are returned if the obj use default bit is set or use default context bit is set.   These will need to be handled in code if there are page specific values as well.</li>
			<li class="li_inner">If no page is used as an object filter, then all the objects of a given context, object type (if any) and setting type (if any) will be returned.</li>
			<li class="li_inner">If no object type is used as an object filter then objects of all types will be returned.</li>
			<li class="li_inner">If no setting type is used as a filter, then setting values of all types will be returned.</li>
			<li class="li_inner">The returned set is ordered first by the object special order, then by object name.  If the special ordering is not set, then the object name will take over.</li>
		</ul>
		
		<h3>&starf;hfwn_get_ctx_vals</h3>
		
		<p>As before, there is a helper function for <i>$hfwgcv</i> called <i>hfwn_get_ctx_vals</i>, or its first class alias <i>$hfwngcv</i>.  The general help for that follows:</p>

		<ul>
			<li>INPUT: (<b>$named_ctx</b>=null, <b>$named_obj_type</b>=null, <b>$named_set_type</b>=null, <b>$named_asc_pg</b>=null,  <b>$named_val_pg</b>=null, <b>$get_def_bit</b>=false, <b>$loc_filter</b>=null)</li>
			<li class="li_inner"><b>$named_ctx</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is the text alias name for a context, though the default context is assumed so only use if another context is needed&mdash;can be an array of named values)</li>
			<li class="li_inner"><b>$named_obj_type</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is the object type name alias to be used instead of the id integer&#8212;see <b>$obj_type</b>&#8212;used as a filter; either single string value or an array of strings.)</li>
			<li class="li_inner"><b>$named_set_type</b>	<i>not required</i>, <i><b>default value=null</b></i> (this is the text setting type name alias to be used instead of the id integer&#8212;see <b>$set_type</b>&#8212;used as a filter)</li>
			<li class="li_inner"><b>$named_asc_pg</b> <i>not required</i>, <i><b>default value=null</b></i> (this is always the pg tag of the object associated page, see <b>$asc_pg</b>, use as a filter)</li>
			<li class="li_inner"><b>$named_val_pg</b> <i>not required</i>, <i><b>default value=null</b></i> (this is always the pg tag of the setting value page, see <b>$val_pg</b>, use as a filter)</li>
			<li class="li_inner"><b>$get_def_bit</b> <i>required</i>, <i><b>default value=false (changed)</b></i> (retreive fallback default values if true for page values or ctx values if they have been set and active for the object.  Will be retrieved in addition to any specific page or ctx values.)</li>
			<li class="li_inner"><b>$loc_filter</b> <i>not required</i>, <i><b>default value=null</b></i> (a single location string or array of location strings to search on for values.)</li>
			<li class="li_inner">&nbsp;</li>
			<li>Typical INPUT example, calling a context, object type and setting type by name: <b>$hfwgcv('ctx1', 'frm_obj', 'html')</b>.</li>
			<li>Getting the default context values for objects associated with the given page: <b>$hfwngcv(null, null, null, 'pg')</b>.</li>
			<li class="li_inner">&nbsp;</li>
			<li>OUTPUT: The output is the same as for <i>$hfwrav</i>.</li>
		</ul>
		
<!-- hfw_get_pg_list -->
		<h3>&starf;hfw_get_pg_list</h3>
		
		<p>To help with vetting page references on templates and other housekeeping duties, there is  <i>$hfwgpl</i> or <i>hfw_get_pg_list</i>.  The general help for that follows:</p>

		<ul>
			<li>INPUT: (<b>$pg_ctx</b>=null)</li>
			<li class="li_inner"><b>$pg_ctx</b>	<i>not required</i> (the string label of the page context id, the default page context is set as def_pg_ctx.)</li>
			<li class="li_inner">&nbsp;</li>
			<li>OUTPUT: The output array is <b>array[0 ... N]['obj_id'=><i>the page object id</i>, 
		'obj_name'=><i>the page object name (page name)</i>, 
		'obj_dsr'=><i>the page object description (page description)</i>, 
		'url_tag'=><i>the page object location (the page url tag id)</i>, 
		'acs_str'=><i>the page added security feature if you need one</i>, 
		'pg_id'=><i>the page id from the pgs table, not the pg_objs table</i>, 
		'pg_ctx_id'=><i>the page context id&#8212;no relation to object value contexts</i>, 
		'pg_ctx_name'=><i>the page context name</i>, 
		'pg_ctx_lbl'=><i>the page context label</i>, 
		'act_bit'=><i>the page state</i>]</b>.</li>
		</ul>
		
		<h3>&starf;hfw_get_acs_str</h3>
		
		<p>To retrieve any security access string for an object, there is  <i>$hfwgas</i> or <i>hfw_get_acs_str</i>.  The general help for that follows:</p>

		<ul>
			<li>INPUT: (<b>$obj_id</b>=null)</li>
			<li class="li_inner"><b>$obj_id</b>	<i>required</i> (the numerical id of the object--gotten from another query in most cases.)</li>
			<li class="li_inner">&nbsp;</li>
			<li>OUTPUT: acs_str value (string).</li>
		</ul>
		
<!-- hfw_get_all_locs -->
		<h3>&starf;hfw_get_all_locs</h3>
		
		<p>For auto-discovery on a page and looping through all page locations there is  <i>$hfwgal</i> or <i>hfw_get_all_locs</i>.  The general help for that follows:</p>

		<ul>
			<li>INPUT: (<b>$url_tag</b>=null, <b>$named_pg_ctx</b>=null, <b>$named_obj_type</b>=null, <b>$act_obj</b>=true)</li>
			<li class="li_inner"><b>$url_tag</b>	<i>not required</i> (the &quot;page name&quot; of interest.  This is the same tag used to get values.)</li>
			<li class="li_inner"><b>$named_pg_ctx</b> <i>not required</i> (the named page context, not a value context.)</li>
			<li class="li_inner"><b>$named_obj_type</b>	<i>not required</i> (the type of object.)</li>
			<li class="li_inner"><b>$act_obj</b>	<i>not required, default value=true</i> (If true only active objects and actively used objects will be returned, otherwise, any)</li>
			<li class="li_inner">&nbsp;</li>
			<li>OUTPUT: The output array is <b>array[0 ... N]['location'=>location tags on the page, 
		'obj_id'=><i>the id of the object associated with the location</i>, 
		'obj_name'=><i>the name of object associated with the location</i>, 
		'acs_str'=><i>the arbitrary access string assigned to the object</i>, 
		'obj_act_bit'=><i>true if the object is active, or false</i>, 
		'brg_act_bit'=><i>true if actively used on a page, otherwise false</i>.]</b>.</li>
		</ul>
		
		<p>Two possibly useful helper functions in the library are <i>get_hfw_pgnum_from_url_tag($tag=null)</i> and <i>get_hfw_ctx_id_from_lbl($ctx=null)</i>, which return the page id and the context id from their text aliases.</p>

<!-- GUI Class Library -->
		<h1>Importation from the GUI Class Library</h1>

		<p>If you would like to import GUI classes into the export library, please keep the following in mind:</p>

		<ol>
			<li>The classes will have to be altered to use the <i>hfw_mysqli_obj</i> class calls instead of the regular <i>mysqli_obj</i> class calls.</li>
			<li>The class names may conflict with existing classes in the project.</li>
			<li>Limited descriptions of the classes are provided.</li>
			<li>These class calls will mostly be useful for probing individual tables in the HFW database.</li>
			<li>Insert and Update classes will come with security risks.</li>
			<li>Some of the GUI classes might be useful in the project class library, instead of the HFW export library, such as the <i>mysqli_obj</i> classes and the <i>html.obj.classes.php</i> library.</li>
			<li>However, not much documentation exists for those in the available library files.</li>
		</ol>

<!-- Security -->
		<h1>Security Directives for Using the GUI on the Production Server</h1>

		<p>If you need to use the GUI on the production server, you will need to set up some minimal security.  Otherwise, the world will be able to alter the website!  Do this at your own risk!</p>

		<ul>
			<li>There are no guarantees that any simple security strategy will work against attacks.</li>
			<li>Unless you have complete control over the hosting environment, you will not know if the security directives can be overridden, or if the web server has some flaws in its setup.</li>
			<li>The simplest directive is to restrict the GUI to particular IP addresses, such as the directive <i>Require ip www.xxx.yyy.zzz</i> in Apache server.</li>
			<li>A slightly more flexible approach is to have a web server-style login available for the GUI.  This will necessitate some sort of HTTPS be used.</li>
			<li>Anything beyond this will require changes to the existing GUI code.</li>
			<li>The GUI will need its own sub-domain or at least its own directory to not conflict with the main website.</li>
		</ul>

<!-- Performance -->
		<h1>Performance of the Output Library Calls</h1>

		<p>There is an art to getting better performance out of any website.  Some standard advice is warranted.</p>

		<ul>
			<li>Make sure all tables are fully indexed and optimized.</li>
			<li>Cache queries.</li>
			<li>Cache PHP pages.</li>
			<li>Run the tables in RAM.</li>
			<li>Optimize the main <i>hoopla_get_obj_val</i> query for your environment.</li>
			<li>Optimize MySQL and PHP settings.</li>
		</ul>

<!-- Exporting Static HTML -->
		<h1>Exporting Static HTML Pages</h1>

		<p>You will not need to export any HFW classes if you just use the HFW to create static HTML.  That is, you can create a website using the HFW and save the HTML of each page statically.</p>

		  <!-- <div class="content_imagetext">
		    <div class="content_image">
		      <img src="images/image1.jpg" alt="image1"/>
	        </div>
		  </div>--><!--close content_imagetext-->
		  

           <div class="content_container">
		    <p>Can&apos;t get enough help? Try our FAQ.</p>          
		  	<div class="button_small">
		      <a href="hoopla.fw.help.faq.php">FAQ</a>
		    </div><!--close button_small-->		  
		  </div><!--close content_container-->		
	  
		  <div class="content_container">
		    <p>Need help on creating and maintaining projects?</p>
		  	<div class="button_small">
		      <a href="hoopla.fw.help.create.prj.php">Projects</a>
		    </div><!--close button_small-->
		  </div><!--close content_container-->

		</div><!--close content_item-->
      </div><!--close content-->   
	</div><!--close site_content-->  	

<?php
	include($incpath . "std.footer.php");
?>

  </div><!--close main-->
  
</body>
</html>
