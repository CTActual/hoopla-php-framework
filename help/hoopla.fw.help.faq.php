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
	$sub_banner = "Fortuitously Answered Questions";
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
		  <h1>F. A .Q .</h1> 

		<p><b>Help!  I need more help!  Where is it?</b></p>

		<p><i>Hopefully by the time you read this more help will be available.  There should be enough here to get you started, however.</i></p>

		<p><b>I don&apos;t understand how this FW makes my life easier.  What&apos;s the point?</b></p>

		<p><i>The ability to create new pages on the fly, copy over objects and then populate page-specific values takes a ton of code that you don't need to replicate in your project database.  It&apos;s been done for your already in the Hoopla FW.  Then, the ability to refactor from a simple GUI is also remarkable.</i></p>

		<p><b>I always use the XYZ framework.  It&apos;s a lot better!  Why should I switch to Hoopla?</b></p>

		<p><i>There are a lot of frameworks out there, but they tend to get a bit overextended.  Maybe you would just want something really easy for a change?</i></p>

		<p><b>How can I possibly use this with WordPress?</b></p>

		<p><i>WordPress has its uses, but we wouldn&apos;t see the point of trying to manage it with Hoopla.</i></p>

		<p><b>What kinds of projects is the HFW good for?</b></p>

		<p><i>We&apos;d recommend small to medium sized projects with no more than three locally collaborating contributors.  You will need to roll your own security (which isn't that hard) to collaborate over the Interwebs.  The framework favors projects that favor well segregated templating pages that require many what we call HFW &quot;pages&quot;.  That is, if you have several pages to create that have the same basic layout, then the ability to reuse objects makes the HFW ideal for your purposes.  There&apos;s no limit with creating different layouts, but if every page has a different layout then you will need to decide how to juggle having different templates, different contexts, or different objects.  All of these choices have their pros and cons.</i></p>

		<p><b>Where&apos;s the magic?</b></p>

		<p><i>A lot of frameworks have a lot of features, particularly regarding database abstraction, that seem magical, but often quite limiting.  For small projects you are better off handling the database yourself.  And we are refering here to your project database, not the Hoopla FW database, which is handled for you.  Other magical features, such as using lots of meta-code and Eval statements to compile it with a ton of javascript often are a huge waste of resources.  Whereas other frameworks use meta-code, we use a database, for 99% of the same results.  We also provide a GUI and it&apos;s a lot easier to use.</i></p>
		
		<p><i>The HFW is a low level framework, not one that tells you how to code.  When using the framework, we find that we have to pull many rabbits out of many hats, so you will need to come up with your own solutions too, and hopefully put them into a toolbox for future use.  You can store object settings according to your own parsing rules; do string replacement your own way; you can use the framework to reference objects within the framework, thereby allowing you to chain things together.  It&apos;s all very  flexible.  Use your first project with the framework as a testing ground for what works for you.  Try out the sample project too.</i></p>

		<p><b>What about performance?</b></p>

		<p><i>Performance should be pretty good if you maintain the HFW database indexes.  It&apos;s a comparatively small database for performance considerations.  With some work, you could probably load it into ram for even better performance, though MySQL query caching is basically that already.</i></p>

		<p><b>Where&apos;s the Armenian version?</b></p>

		<p><i>While an Armenian version would be great, our team is too small and too parochial to create one.  American English for now, thanks.</i></p>

		<p><b>Why are there no GUI settings?</b></p>

		<p><i>We don&apos;t have enough feedback from users yet to know what settings would be a good idea, though the basic layout seems to work just fine as is.  Most of the time, settings rarely get used to the degree they are requested, but no suggestion is too small.</i></p>

		<p><b>Egad!  Why is the GUI for the Values-by-Page and Values-by-Object pages so complicated?</b></p>

		<p><i>These are the main pages that you will be using when working on a project, and have to deal with a lot of interrelationships between pages, contexts, setting types and objects.  They aren&apos;t that complicated, in reality, once you are familiar with how the framework goes.  They are visually busy, but actually have a linear top-down workflow pattern.  The basic workflow for the GUI is from left to right and top to bottom.  The exception would be when you add contexts to your project, which we put to the right because of their general indepedence from the page-object theme.  On-page help is provided in the left column on each page.</i></p>

		<p><b>Is the code well documented?</b></p>

		<p><i>Of main interest for anyone developing a new project using the HFW are the descriptions of the export library function calls, which are located both in the source code and also here in the help system.  The demo project code is also well documented for the most part, with comments throughout, though in-line with the code and not presented as a how-to manual.  The code that runs the GUI and ancilliary libraries is mostly only of interest to HFW developers and not users, but the PHP HTML Object Classes library code could be of use to a user, though it is not super-well documented.</i></p>

		<p><b>I encapsulate my queries in classes and functions.  Now you want me to store them in a database?</b></p>

		<p><i>Not necessarily.  You have the choice to do it or not.  It is true that exposing queries directly on a page will result in repetitive boilerplate code compared to something like a private class library, but in a small project, or even a bigger project, having all the queries for a set of pages with a similar theme, but important differences, accessible in one place is more convenient when refactoring or debugging.  The framework can abstract away many issues, by for example, creating an object that stores first-class variable function calls on a per page/context basis.  You can parse these calls, even if they require input parameters, depending on how you store and retrieve them.  This allows you to have libraries of code that can be called as necessary without storing the code directly in the framework.</i></p>

		<p><b>Why go the publishing to a website route instead of putting the whole GUI framework on the production server?</b></p>

		<p><i>Nothing prevents a user from either hosting the application on their production server or writing trackback queries in their main application to change values in the HFW database.  However, having a single master version of the site HFW database will avoid confusion and not putting the whole thing on the production server will be more secure.  Another way to think about this is that you probably would not edit your PHP project code on your production server either, and this is not a Wordpress clone.</i></p>

		<p><b>Is a page also an object?</b></p>

		<p><i>Yes.  However, it is automatically assigned to itself.  The reason for this is that it becomes possible to assign values to it directly if the end user finds that cleaner or easier.  However, only on the Values&dash;by&dash;Object page.</i></p>

		<p><b>Why isn&apos;t there a way for me to automatically download and install my XYZ 3<sup>rd</sup> party library with its 80,000 dependencies so that I can get my favorite website widgets working?</b></p>

		<p><i>This is a low&dash;level framework, so we await someone to create higher&dash;level tools and automation in some future iteration.</i></p>



		  <!-- <div class="content_imagetext">
		    <div class="content_image">
		      <img src="images/image1.jpg" alt="image1"/>
	        </div>
		  </div>--><!--close content_imagetext-->
		  

		  <div class="content_container">
		    <p>Need help on creating and maintaining projects?</p>
		  	<div class="button_small">
		      <a href="hoopla.fw.help.create.prj.php">Projects</a>
		    </div><!--close button_small-->
		  </div><!--close content_container-->

           <div class="content_container">
		    <p>More help on the export library.</p>          
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
