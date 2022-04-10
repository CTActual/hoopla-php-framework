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

require_once('hoopla.fw.rel.path.php');

include($classpath . "html.obj.classes.php");

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
	$sub_banner = "Populate PHP Templates Using MySQL";
	include($incpath . "std.banner.php");
?>
    </header>
    
	<div id="site_content">		
	
<?php
	include($sidepath . "std.sidebar.php");
?>

	  <div id="content">
        <div class="content_item">
		  <h1>Welcome To Your PHP Website Creator</h1> 
          <p>Do you find most PHP frameworks too complicated or difficult to learn and use?  Do they seem too large?  We&apos;ve rethought the problems with creating simple websites that need dynamic content but don&apos;t benefit from large frameworks.  
		If you create a PHP template page you can now populate it with dynamic elements in their own contexts through this UI.  The values are stored in a MySQL database.  This is kinda meta.  We don&apos;t deal with your data.  We are the framework for 
		displaying your data.  Your queries, pointing to your database, are saved in their own contexts in our Hoopla FW database.  They are stored in the Hoople FW database and then retreived as object values to be used by your template.  HTML elements can 
		also be stored.  JS scripts&sol;links and CSS scripts&sol;links can also be stored.  Even PHP code can also be stored.  Change the object value and change what gets loaded on the page.</p>   		
		  
		    <p>Say you have a table that displays data.  You can store the layout information for each page, the query for each page, the css class descriptors for each page, etc. in the Hoopla FW database and then retrieve them when the page is called.
			Pages are in a sense a special object type in Hoopla, where you call your template page (eg. example.php) and then the page id (eg. example.php?pg=cars).  The template summons the objects for that page using the Hoopla FW object value 
			class and then populates the template&apos;s skeletal code with all the retrieved values.  The PHP of the template uses the retrieved values the create the final webpage.</p>

		  <!-- <div class="content_imagetext">
		    <div class="content_image">
		      <img src="images/image1.jpg" alt="image1"/>
	        </div>
		  </div>--><!--close content_imagetext-->
		  
		    <p>Do you find Wordpress&trade; too restrictive?  Since you create your own templates, you are in control.  You create your site locally and then publish your stuff, the Hoople FW database and classes to your public website for consumption.
			The published Hoople FW database can be essentially read only, thereby avoiding security issues associated with CMS frameworks.  (We don&apos;t recommend porting the framework UI code you see here to your site unless under some 
			kind of security umbrella of your choosing).  Remember, you can create an article or &quot;blog&quot; publishing site that uses your own database to host the contents, and uses the Hoople FW database to host the layout and queries, so you do not need
			the FW to host your actual content.  You can probably use the Hoopla FW in conjunction with other frameworks.  It&apos;s very unrestrictive.</p>

		    <p>Annoyed by the limits on queries imposed by other frameworks?  While frameworks can make dealing with databases easier, they tend to allow only the simplest of queries.  Here, you write the queries you want, or call a stored procedure and process 
		them as you need to.  You are not technically limited to MySQL for your own database.  While you would need to host MySQL for all Hoopla FW objects, and retrieve values using the associated classes, the queries retrieved (even the connection strings), 
		can point elsewhere.</p>

		    <p>How does all this help you?  First, you finally get a framework with a UI!  The hard parts are handled for you.  You get to use 100% flexible templates of your own design.  They can retrieve objects, but don't have to&mdash;acting like normal PHP pages. 
			You get both object and object value reuse, so if you like them, reuse them.  Or assign them to a particular context only.  You can change templates around independently of object values, pages, etc. and change pages, objects and their associated values 
			independently of templates.  You can find objects and their values very quickly through the UI, rather than hunting through code.</p>

		  <div class="content_container">
		    <p>Need more help?  Try our FAQ.</p>
		  	<div class="button_small">
		      <a href="help/hoopla.fw.help.faq.php">FAQ</a>
		    </div><!--close button_small-->
		  </div><!--close content_container-->

          <div class="content_container">
		    <p>Need help on creating and maintaining projects?</p>          
		  	<div class="button_small">
		      <a href="help/hoopla.fw.help.create.prj.php">Projects</a>
		    </div><!--close button_small-->		  
		  </div><!--close content_container-->		
	  
          <div class="content_container">
		    <p>Need more help on the export library to get output?</p>          
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
