<?php
/*
Copyright 2009-2025 Cargotrader, Inc. All rights reserved.

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
	$sub_banner = "General Hoopla FW Help";
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
		  <h1>Congratulations on Getting This Far!</h1> 

		<p>You&apos;ve managed to install Hoopla on your server and can see this Help page.  Let&apos;s make sure you&apos;ve got the database up and running right.</p>

		<ul>
			<li>The Hoopla database needs to be installed for this IDE to work properly.</li>
			<li>What you are looking at (what this is), is the GUI tool for inputting and managing framework object values into the Hoopla database so that your project web pages can 
				pull out those values later on the real website.</li>
			<li>We&apos;ve avoided many installation issues and headaches by not making the Hoopla GUI self-hosting.  This is just basic HTML, PHP and Javascript.</li>
			<li>To install the database you will need to get hold of the sql installer script, give the database a proper name and then run the script in something like PHPMyAdmin.</li>
			<li>Then you will need to make sure you have the correct connection string information in the default <i>hfw.mysqli.info.php</i> file for both the database name and users.  This file is in classes/info.</li>
			<li>The Hoopla database users are created using the <i>create.hfw.usrs.sql</i> file.</li>
			<li>If you are running into errors, you may need to set up MySQL to allow for the installers to run correctly, or you are running a version of MySQL that is too old or too new 
				for the installers.</li>
			<li>In case of errors, you will need to correct the scripts&#8212;particularly the create users script to the syntax for your version of MySQL.</li>
			<li>We have set everything in the database to default to MySQL 8. AKA &quot;DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci.&quot;</li>
			<li>These settings may not be available for your version of MySQL, so you might have to replace those with your desired settings in the installer script.</li>
			<li>Most text fields are sized for speed and readability.  However, given the use of HTML Entities, you might find them restrictive.  Size-up as needed, careful to override the save functions too.</li>
			<li>For obvious security reasons, change the name of the database, users and passwords to something other than the defaults.</li>
			<li><b>To see if the database is installed properly, the other pages in this UI should work, even though the database is largely blank to start out.</b></li>
			<li>For example, click on the <i>Types</i> page to see if the types have loaded without errors.</li>
			<li>If everything is good, we can proceed.</li>
		</ul>

			<h1>Let&apos;s Proceed!</h1>

		<p>Let&apos;s outline a way to manage using Hoopla for your projects</p>
		<ul>
			<li>Hoopla is not really collaborative software.</li>
			<li>In theory a small team could use Hoopla and work together over a network.</li>
			<li>However, you would need to trust all the users; changes are not automatically displayed on the various pages; and changes are not tracked for undo purposes.</li>
			<li>What you probably should do is create one database per project&sol;website, with each database having its own name and connection information.</li>
			<li>The connection information for that project database would go in its own file in classes/info.  The filename should end with &quot;.php&quot;</li>
			<li>Hoopla now handles mulitple projects&sol;databases, but you publish each Hoopla project database with the corresponding project on the production server.  You should not mingle projects in one HFW database.</li>
			<li>Now you can use the new <b>Projects</b> selection page to select which database you'd like to connect to.  Make sure the web server can read&sol;write in the classes&sol;info folder.</li>
			<li>The goal here is to create a simple GUI tool framework for a single developer who wants to create and manage websites easily.</li>
			<li>Use the default <i>hfw.mysqli.info.php</i> connection string file in classes&sol;info as an example of how to create a new file for a new project.</li>
			<li>If you want to create an export tool to publish to your hosting provider through some sort of API, that would be great, though daunting.</li>
		</ul>
		
		<p>Creating Websites</p>
		<ul>
			<li>There are three parts to a Hoopla project.  The first are the PHP templates, which you create as you like and which uses the output library (exported classes and functions) on the production server.</li>
			<li>The second is this UI, which allows the Hoopla database to be populated and updated with the &quot;stuff&quot; needed by the template(s).</li>
			<li>The third part is the backend software that processes requests from the template through the HFW database and project database and back to HTML generation on the server or client.</li>
			<li>This is a low&dash;level framework and therefore very powerful and extremely flexible.</li>
			<li>You can do all sorts of savvy things, like create a standard website for yourself that is the starting point for your projects, and then reuse it over and over.</li>
			<li>You may end up with a catalog of starting point project websites as examples of what you can do for development.</li>
			<li>Anything that is built up ahead of time will save time for the actual project.</li>
			<li>You can use Hoopla to populate the final website templates to the extent you are able to abstract your needs to the framework, or very lightly, as you like.</li>
			<li>The framework is so flexible that you can start out with a small amount of handholding and increase your reliance on it as you get more proficient.  There isn&apos;t a high hurdle to clear just to get started.</li>
			<li>Hoopla can hold almost anything that an HTML page can need, but a balance should be struck between how much is dynamic in practice and how much is really static.</li>
			<li>You need to decide how to mix the content in the Hoopla database with the content in your main project database.</li>
			<li>It is not recommended that you modify the Hoopla database (adding tables and&sol;or fields) for your project since this can break things and lead to update hell.</li>
			<li>You can change the background settings for types in the UI as you like.  You are encouraged to make both object setting (arbitrary or specific) contexts and page contexts as needed for your project.</li>
			<li>You might be coordinating several databases in your project.  A HFW database can store are things like connection strings and queries that deal with those other databases.</li>
			<li><b>Hoopla is not the best content management system.</b></li>
			<li>You might be tempted to use Hoopla for content management, but that is probably a mistake since it is statically published and read only.</li>
			<li>Think of Hoopla as something you can use to create a content management system (CMS) instead.</li>
			<li>We have created a demo project/website available for you to get a handle on things, and which contains potentially useful helper classes for parsing and output.</li>
			<li><div class="button_link"><a href="help/hoopla.fw.help.create.prj.php">More on Creating Websites</a></div></li>
		</ul>
		
		<p>Publishing</p>
		<ul>
			<li>This GUI is for creating a project, as we have said.  It isn&apos;t part of the final project once completed.</li>
			<li>What you will be publishing to or running on the hosting website server (production server), are the files for your project you created, the 
			<li style="list-style-type:none;">Hoopla Output Library files&#8212;generally in their own directory&#8212;and installer scripts for any databases and users the project needs.</li>
			<li>This GUI should not be part of that install.</li>
			<li>You will probably have a testing version of your project locally so you can see it all come together and fix any issues that arise before publishing.</li>
			<li>This new for 2024 version can handle multiple projects through one GUI as long as the database connection files follow a convention.</li>
			<li>Installer scripts are often generated using tools like PHPMyAdmin, which have export sql commands built into their UIs.  It&apos;s not something available yet through this GUI.</li>
			<li>Production server environments are rarely identical to local development server environments.  This may mean that code that works locally will not work when published.</li>
			<li>This is often painfully true with things like PHP error handling, UTF-8 support, MySQL collation issues and file paths.</li>
			<li>You will need to get a handle on all the issues regarding last minute environment errors by being familiar with both your local and final environments.</li>
			<li>We offer the Contexts feature as a way to deal with different environments if necessary.  If you match your server vars to a context you can retrieve values based on environment automatically.</li>
			<li>Contexts are actually more powerful than that, but hint&dash;hint.</li>
			<li>Keep in mind that contexts will not help you get your HFW database running and connected to your production website though.</li>
		</ul>
		
		<p>Hoopla Output Library</p>
		<ul>
			<li>We provide a basic stockade of exported functions and classes that get published with the databse to the production server for retrieving output on the templates.</li>
			<li>You can certainly create more if you like.  Once you are familiar with how the Hoopla database works, you can easily expand on the functions and classes.</li>
			<li>The stock functions will help you retrieve information out of the Hoopla database, but parsing this information and outputting it to generate HTML is up to your template and project PHP libraries.</li>
			<li>You will need to make sure that the connection string information on the exported hfw.db.info.php file is correct, and file paths are well understood.</li>
			<li>We have revamped the two most important library calls for 2024.  They are mostly backwards compatible, but may cause some problems if you update.</li>
			<li><div class="button_link"><a href="help/hoopla.fw.help.export.lib.php">Export Library Help</a></div></li>
		</ul>
		
		<p>Updates to Projects and Republishing</p>
		<ul>
			<li>The Hoopla database should be fairly static once published (probably through an sql file import).</li>
			<li>You should maintain the project locally in case you need to publish updates.</li>
			<li>This should be secondary to other aspects of the project that need updating, such as photos in a photo library.</li>
			<li>That&apos;s dynamic content that really only exists on the production server</li>
			<li>A Hoopla project by itself&#8212;through this UI and a populated Hoopla database&#8212;should not be dependent on anything in the rest of your project <i>per se</i>, such that you can't perform an update.</li>
			<li>Your local template should still populate normally without the dynamic content already on the production server.</li>
			<li>It just might be somewhat empty.</li>
			<li>Be careful with any updates, though, since this can break things on the production server.  For example, a change in a query or CSS link.</li>
			<li>What is likely to happen, though, are changes in the other databases in your project that then require changes in the queries stored in the Hoopla database;</li>
			<li>Or some change in the layout that requires an update.</li>
			<li><b>Tempting as it is to put this UI on the production server, it is not recommened for security reasons.</b></li>
			<li>The exported library code is safe to use on a production server, since it does not do updates or deletes,</li>
			<li><b>But the UI code has no innate security features for use on a production server; and you will need to handle data encryption and other data security in your main application.</b></li>
			<li>Nothing prevents the main application from making direct changes to the production server Hoopla DB,</li>
			<li>But any local version will then be out of sync with the production version.</li>
		</ul>
		
		<p>Enhancements to Hoopla</p>
		<ul>
			<li>There are essentially three types of updates or enhancements that could happen.</li>
			<li>Enhancements from On Top, which will be uncommon and try not to break things.</li>
			<li>Forks or third party enhancements, which we have no control over and could break things.</li>
			<li>Your own personal enhancements, which we have no control over and could break things, but you have control over and can fix.</li>
			<li>As the Hoopla dev team is quite small, and the database already extremely abstract, there aren&apos;t a lot of changes to the database envisioned.</li>
			<li>Changes to the UI are more likely, though not likely to break anything.</li>
			<li>Changes to the export library are not likely to break anything, but that can&apos;t be guaranteed.</li>
			<li><b>Changes for 2024:</b></li>
			<ol>
				<li>There is a new <b>db_meta_data</b> table in the database.  An install script is provided.  This is needed for handling multiple projects in a nice way.</li>
				<li>In addition to &quot;1&quot; above, there is a new <b>Projects</b> page in the UI for selecting the current working project on your desktop.</li>
				<li>There is a new field in the <b>pg_pg_obj_brg</b> table: <b>use_def_ctx_bit</b>, which allows users to fallback to the <b>def_ctx</b> when the requested context has no value.</li>
				<li>As per above, the UI now reflects this new field and allows users to make the necessary setting changes for <b>use_def_ctx_bit</b>.</li>
				<li>As per above again, the export library calls for retrieving values and context values now reflect the use of <b>use_def_ctx_bit</b> and have been revamped.  Behavior might be slightly different from before and things could break, requiring passing extra parameters to these calls or changing the default settings on an object.  For example, <b>hfw_return_value, hfwn_return_value, hfw_get_ctx_vals </b>and <b>hfwn_get_ctx_vals</b> all have <b>$get_def_bit</b> as an input param and would be affected by this change.</li>
				<li>Further: some of these calls now output the <b>use_def_ctx_bit</b> value.</li>
				<li> Now users can change the ordering of pages on <b>Pages</b> UI page, instead of going through <b>Values-by-Object</b> as before.</li>
			</ol>
			<li><b>Changes for 2025:</b></li>
			<ol>
				<li>Some bugs affecting PHP 8+ installs have been eliminated.</li>
				<li>Assigning and object to existing pages and auto-creating a special order is now more consistent with typical user expectations, and works better with the object type dropdown count.</li>
				<li>The export library help shows better that some input parameters can now be arrays.</li>
				<li>The <b>hfw_get_pg_list</b> function now can filter out inactive pages and can output based on columns as well as rows to make grabbing a list of properties into an array easier.  This now chains nicely with <b>hfw_get_ctx_vals</b> and <b>hfwn_get_ctx_vals</b>.</li>
			</ol>
		</ul>
		
		  <div class="content_container">
		    <p>Can&apos;t get enough Help?  Try our FAQ!</p>
		  	<div class="button_small">
		      <a href="help/hoopla.fw.help.faq.php">FAQ</a>
		    </div><!--close button_small-->
		  </div><!--close content_container-->

          <div class="content_container">
		    <p>More on help creating websites.</p>          
		  	<div class="button_small">
		      <a href="help/hoopla.fw.help.create.prj.php">Projects</a>
		    </div><!--close button_small-->		  
		  </div><!--close content_container-->		
	  
          <div class="content_container">
		    <p>More help on the export library.</p>          
		  	<div class="button_small">
		      <a href="help/hoopla.fw.help.export.lib.php">Output</a>
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
