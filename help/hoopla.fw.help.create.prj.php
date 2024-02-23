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
	$sub_banner = "Help On Creating Projects";
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
		  <h1>Creating Projects And&sol;Or Websites</h1> 

		<p><b>The Basics</b></p>

		<ul>
			<li>A project website is composed of one or more pages, even if they share a lot of features in common.</li>
			<li>A Hoopla project template will call several pages through some sort of API (either simple GETs, REST or elsewise).</li>
			<li>A page&#8212;each of which can have a name&#8212;will be linked to several Hoopla FW objects.</li>
			<li>Objects are created, as needed, and then linked to pages.  You can have one object link to several pages.  Linking an object to a page is called an Assignment.</li>
			<li>This UI populates objects final values, which can be per page or as a default for all pages, and of potentially several setting types and in potentially several contexts.</li>
			<li>The template will then call the object value and process it with PHP as needed to generate the final page.</li>
			<li>Don&apos;t duplicate objects on a per page basis.  They are designed to be reused and shared amongst the pages through mulitiple assignments.</li>
			<li>An object that has the same value on every page for a given template is probably better off just being part of the template.  (Though See Below.)</li>
			<li>Different templates generally call different pages, but nothing prevents them from sharing pages if this is more convenient.  You can organize this better through page contexts.</li>
			<li>A template generally doesn&apos;t ignore objects, and this is not recommended for refactoring considerations, but nothing prevents PHP from having business logic that overrides the assignment.</li>
			<li>The location feature allows one to assign an object to pages with different recall tags, so this alone can clean up the PHP business logic on the template.</li>
			<li>Pages are of the specific object type &quot;Page&quot; that is automatically assigned, and required for the framework.</li>
			<li>You will need to assign non-page objects to the other categories available (page object types), but how you do so is up to you.</li>
			<li>You can use object types as a way of organizing your project, but their main function is to allow you to create your own parsing library code.  See the demo project for examples of how this works. </li>
			<li>Types can be added, disabled and&sol;or changed at will with the type management features.</li>
			<li>It is strongly recommended that any changes to the types be done before work on the project starts.  The defaults are a good starting point for most projects.  Later changes could result in tons of confusion.</li>
		</ul>

		<p><b>Prerequisite Knowledge</b></p>

		<ul>
			<li>No knowledge of PHP or MySQL is required to use the GUI, but one would probably need to know HTML, CSS and perhaps Javascript.</li>
			<li>One should be familiar with PHP in order to make templates, as well as HTML, CSS and Javascript.</li>
			<li>Knowing MySQL will allow the developer to create better website templates using a project specific database, but this is not a requirement for a project.</li>
			<li>You will need to know how to set up a PHP website on a local server in order to run the GUI, with working MySQL and the correct settings for each.  This is just for installation.  Once the GUI is running, the PHP and MySQL are transparent to the user.</li>
		</ul>

		<p><b>The Basic Steps</b></p>

		<ol>
			<li>Design your website.</li>
			<li>Break down your website into the most convenient number of templates.</li>
			<li>Figure out the objects to put into Hoopla from your parsed template.</li>
			<li>Create your pages in Hoopla.</li>
			<li>Create your objects in Hoopla.</li>
			<li>Assign the objects from your templates to the correct pages.  This can be done when you create the objects or later.  You can also clone pages with the same set of objects.</li>
			<li>The page assignment &quot;locations&quot; (aka &quot;references&quot; or &quot;names&quot;) will be unique per object, but can vary from page to page.</li>
			<li>For example, on page1 <i>object1</i> can be at location &quot;one&quot;, and <i>object2</i> can be at location &quot;two&quot;, and certainly not also at &quot;one&quot; in conflict with <i>object1</i> (see the example below).</li>
			<li>However, nothing prevents <i>object2</i> from being at location &quot;three&quot; on page2, for whatever reason, or the original &quot;two&quot;.  Having the objects at the same locations on different pages generally makes the templates easier to manage.</li>
			<li>Locations have nothing to do with physical location of the object on the rendered page or even some sort of ordering on the template.  They are just unique references for pulling values later.</li>
			<li>Objects can be used more than once per page.</li>
			<li>If your template and object parsing are abstract enough, you will probably be dealing more with contexts than locations, since they allow handling multiple objects in one call.</li>
			<li>Determine if you need more than the default context.  <i>This is almost a certainty.</i></li>
			<li>Give your objects values, both default and per page, and for each context, as necessary.  Defaults get overridden by the page specific value by default.</li>
			<li>You can choose to search for objects in the GUI on a per page or per object basis.  The object values are the same either way.</li>
			<li>Objects can have simulated array values through CSV entries, JSON entries, etc. for things like URL lists or file lists.  There are several PHP functions for handling string list to array conversion.</li>
			<li>If you plan on using preg_replace, str_replace or similar functions for object parsing, be prepared to use a consistent naming convention within object values.</li>
		</ol>

		<p><b>A Simple Example Using the GUI (aka &quot;Hello World&quot;)</b></p>

		<ul>
			<li>Create a page called &quot;index&quot;.</li>
			<li>Create an object of type &quot;element&quot; called &quot;text&quot;.</li>
			<li>Assign &quot;text&quot; to &quot;index&quot; with the location tag &quot;here&quot;.</li>
			<li>Go to &quot;Values-by-Page&quot; and select &quot;index&quot;.</li>
			<li>Select the object &quot;text&quot;, and the setting type &quot;text&quot;.</li>
			<li>Give &quot;text&quot; the value &quot;Hello World&quot; for the page &quot;index&quot;, not the default for all pages.</li>
			<li>A very simple template PHP page (e.g. &quot;page.php&quot;) could look something like:</li>
			<li><div>
<?php
$example = <<<EXAMPLE
<&quest;php<br>
{...include Hoopla FW export library full path before any code. ...}
<br>
if (isset(\$_GET['p']) ) <br> 
{<br>
echo \$hfwrv(\$_GET['p'], 'here');<br>
}<br>
&quest;>
EXAMPLE;

	echo $example;
?>
			</div></li>
			<li>Save the template on your development server.</li>
			<li>In your browser, point to page.php?p=index</li>
			<li>The page output on the browser would of course be just: &quot;Hello World&quot;.</li>
			<li>You could have just entered it as a default value and not called the page (assuming the page name &quot;index&quot; is called with &#36;&#95;GET&#91;&apos;p&apos;&#93;).</li>
			<li>The result would have been the same.</li>
			<li>If you had made the value &quot;&lt;b&gt;Hello World&lt;&sol;b&gt;&quot;, then the output would have been: &quot;<b>Hello World</b>&quot;</li>
			<li>However, your template might need to save the setting as HTML, depending on how it processes text values.</li>
			<li>Create a second page called &quot;page2&quot;.  You can clone &quot;index&quot;, or...</li>
			<li>Assign &quot;text&quot; to &quot;page2&quot; with the location tag &quot;here&quot;.</li>
			<li>Repeating your previous steps, give &quot;text&quot; the value &quot;Goodbye Cruel World!&quot; for the page &quot;page2&quot;.</li>
			<li>For the same template, if &apos;p&apos; gets &quot;page2&quot; then the output would be&#8212;wait for it&#8212;&quot;Goodbye Cruel World!&quot;</li>
			<li>So, you&apos;ve quickly created two web pages with just one template.</li>
		</ul>

		<p><b>More Useful Suggestions</b></p>

		<ul>
			<li>Queries to a project database often need to change from page to page.</li>
			<li>You can have a mobile or cell phone context in addition to a default desktop context.</li>
			<li>Load different CSS or Javascript files on different pages.</li>
			<li>Load different project includes, libraries, etc on different pages.</li>
			<li>HTML meta-tags, labels, formatting, etc. can be adjusted per page.</li>
			<li>Save your links in one place.</li>
			<li>Avoid static code that is hard to refactor.</li>
			<li>Default values allow for per page exceptions on the pages that need them.</li>
			<li>Keep your project code better organized and searchable in the Hoopla DB.</li>
			<li>Create a &quot;Notes&quot; object and a &quot;Notes&quot; arbitrary context to allow you to enter programming guide information as a local GUI helpdesk.</li>
			<li>The better your template is at abstracting the project, the easier it is to segregate work.</li>
			<li>Ideally your project database holds the dynamic real world information.</li>
			<li>The HFW database holds static real world information, PHP page specific instructions for generating dynamic content in HTML and perhaps queries to your project database.</li>
			<li>Your PHP template pages talk to both your project database and the HFW database.</li>
			<li>The less your template pages <i>know</i> about the real world, the better, though this can be hard to achieve.</li>
			<li>There should exist a pre-populated example Hoople project available called &quot;ToDoList&quot; you can use to become more familiar with what&apos;s what.</li>
		</ul>

		  <!-- <div class="content_imagetext">
		    <div class="content_image">
		      <img src="images/image1.jpg" alt="image1"/>
	        </div>
		  </div>--><!--close content_imagetext-->
		  

		  <div class="content_container">
		    <p>Need more help?  Try our FAQ.</p>
		  	<div class="button_small">
		      <a href="hoopla.fw.help.faq.php">FAQ</a>
		    </div><!--close button_small-->
		  </div><!--close content_container-->

           <div class="content_container">
		    <p>More help on the export library. <br>This gets a bit technical.</p>          
		  	<div class="button_small">
		      <a href="hoopla.fw.help.export.lib.php">Output</a>
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
