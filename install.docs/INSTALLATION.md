<pre><h1 style="text-align:center; background-color:ivory;">Hoopla Framework Installation Instructions</h1></pre>
* * *

The following steps serve as an outline (details to follow):

1.  Set up a local PHP and MySQL web (HTTP) server for installing the framework IDE and developing the web application.
2.  Use the MySQL install scripts to set up the Hoopla database and users.
3.  Install the Hoopla Web IDE pages in a local web folder.
4.  Make sure the Hoopla Web IDE pages can connect with the local Hoopla database.
5.  Create your web application project in a separate web folder.
6.  Install the exported Hoopla classes to your project class folder (however your paths are determined).
7.  Make sure your application pages can connect with the Hoopla database.


# System Requirements #
* * *

Any local server that can handle PHP 5.6+ and MySQL 5.3+ should be able to install the IDE and database. PHP versions 7+ recommended, and MySQL 8.0+ recommended. Testing for PHP 8+ has been somewhat limited, and bugs have been recently fixed with more to follow when found. Modern browsers recommended for the IDE. Please read the detail installation instructions for caveats.
Future updates might require a change in the hosting environment.


# Detailed Installation Instructions #
* * *

1. Set up a local PHP and MySQL web (HTTP) server for installing the framework IDE and developing the web application.

	We cannot provide explicit details on creating a web server that can host the IDE and database--there are too many options available.

	A typical setup would be a modern LAMP stack.  As the IDE is a network available application (depending on the web hosting settings), 
the work of designing the website does not have to be on the same machine as the IDE or database.

	If Apache is not desired, then NGINX may be suitable.  For MySQL, the use of MyISAM tables might limit one to the version from Oracle.

	For PHP, the main issue will be error handling, which should not be so broad as to contantly show warnings and notifications that can normally be safely ignored.

	Not too many PHP modules will be needed, but most LAMP arrangements (or NGINX substitutes) will also include PHPMyAdmin for convenience.

	For MySQL, the main issue will be allowing the correct character sets and collations to work properly, which is something that can be a challenge with differing versions of MySQL.

	The database install script uses a UTF8MB4 collation (utf8mb4_0900_ai_ci), but by rescripting the database installation, a generic unicode version should work fine in most cases.

	This caveat is important because of the simple default Latin-1 setups that MySQL frequently comes with and which should be changed quickly after installing MySQL.

2. Use the MySQL install scripts to set up the Hoopla database and users.

	Once the web server is up and running, you will need to use the database and user installer scripts found in "install.scripts" to set up the Hoopla database and users.

	Please be careful to check these scripts against your version of MySQL as more recent versions of MySQL (8+) use a newer syntax, particularly for user installation.

	You will need the correct privileges within MySQL to run these scripts.  The Root MySQL user can certainly do this.

	We recommend changing the passwords to something other than the defaults, since anyone using the default would essentially have a non-secret set of passwords.

	Any changes to the passwords and/or user names need to be reflected in the connection string file "hfw.mysqli.info.php" in the "classes/info" folder, which is needed by the IDE.

	The "hfw.export.lib" folder also contains a default connection string file "hfw.db.info.php", which would also need to track any password or user name changes. One should create both local and server versions of this file with different passwords for security.  This is used by the actual production application to connect to the HFW database and needs the same connection info as the GUI connection string file in "classes/info".  However, the GUI will not need a production server version of the file "hfw.mysqli.info.php".

	We provide create MySQL user scripts for "localhost", though you can swap that out with "127.0.0.1" as you see fit.  If your MySQL settings use something else, then these will need modification.  You can install both or just the most appropriate set.

	More help on this topic is provided in the IDE.  Help in the IDE might not match exactly the help here.  You do not need to have MySQL running or the HFW database installed to read the IDE help pages.  See Step 3 below.

3. Install the Hoopla Web IDE pages in a local web folder.

	Once you have carved out a web folder on the local server for the IDE--the IDE is not installed on the production server--you will need to install all the files in this repo except for the ones in the "install" and "hfw.export.lib" folders and install all root folder PHP files and no non-PHP pages except favicon.ico if you like it.

	These will go in the document root of the IDE website on your local server.  This may also be an alias folder in parallel with other projects.

	Keep the folder structure as is otherwise.  Moving files from one folder to another will almost certainly break things in the IDE.

	Don't mix these files with your project files or other websites on the local server.  They should go cleanly in their own folder.

	If PHP is working properly, you should be able to at least see the help pages in the IDE with proper formatting in your favorite browser once your web server settings can see the HFW GUI folder correctly (and probably restarted).

4. Make sure the Hoopla Web IDE pages can connect with the local Hoopla database.

	If the IDE pages that connect to the database work, and the default information is displayed, then the IDE should be working.

	If you get an error message, you may need to recheck your installation, making sure that MySQL is installed correctly and the connection string information is correct. The file "classes/mysqli.info.php" is pulls in "classes/info/hfw.mysqli.info.php", the default connection string file.  New for 2024, you can add more connection string files to "classes/info" and then select the appropriate file under "Projects" in the GUI to change projects.  This will entail the server copying files over to the "classes/info" folder, so the web server must have write permissions to that folder.  All connection string files must end with ".php".  If you put other files in the "classes/info" folder (not recommended), make sure they don't end with ".php".

	The easiest test is to go to the "Types" page, where at the very least the list of meta-types will be shown in the dropdown box.  Clicking on the "Select Meta Type" button should load the types of the default meta-type for display.

	Tools like PHPMyAdmin can make it easier to see what is going on in MySQL, but you may also need to check the web server (such as Apache) and MySQL error logs to find out what any problem is.

	Please read the help that comes with the IDE for proper usage on developing a project and the ideas behind the framework before starting your first project.

5. Create your web application project in a separate web folder.

	You will most likely be using the Hoopla Framework on one or more PHP-based projects, each of which should get their own distinct installation folder on the local server.

	You can save multiple connection string files in the "classes/info" GUI/IDE folder and then select among them in the GUI under "Projects".  This requires that the web server have write permissions to "classes/info".

	Since your project is likely to be initially blank, you may wish to create a simple index.html and then index.php to verify that at least something is set up properly before proceeding.

	The user is assumed to have sufficient knowledge of the relevant programming languages (PHP, SQL, HTML and Javascript at the very least) to create a new project.

6. Install the exported Hoopla classes to your project class folder (however your paths are determined).

	The files in the "hfw.export.lib" will be needed in your project somewhere to enable you to use the HFW on the output side, beyond just using the IDE for input.

	There are just three files needed, which do not necessarily need to be in their own folder, but can be if that works better.

	The three files for a local install are "hfw.db.info.php", "hfw.export.lib.php" and "hfw.mysqli.class.php".

	Two of these files: "hfw.export.lib.php" and "hfw.mysqli.class.php" are needed on the production server as well, along with a production server version of "hfw.db.info.php".

	More information on the set up on the production server is available as part of IDE help system.

7. Make sure your application pages can connect with the Hoopla database.

	You will need to make a test template that calls some basic information from the HFW database to verify that your project can connect with that database.

	This is a separate installation issue than the IDE connecting to the database, even though both are connecting to the same database.

	More help is provided in the IDE help system on creating templates.

8. Make use of the sample application from our repo

	You should find a sample application that uses the framework available on our repo.  This will guide you on how the framework "works".

9. New for 2024

	We have added a field to the HFW database table "pg_pg_obj_brg" called "use_def_ctx_bit".  There is a file in the "install.scripts" folder, "update.hfw.db.for.2024.sql" that should be run on older databases only to update them to the latest structure.  This new field and changes in the export library framework classes might result in changes in the behavior of previously created projects and they will need to be tested before any production changes are applied.  The same script also adds a new table to the database needed for new multi-project feature to run correctly.  So, running "update.hfw.db.for.2024.sql" in PHPMyAdmin or on the MySQL command line will make both updates.  The new 2024 version will not require this script to be run.
	
10. New for 2025

	We have fixed bugs that were affecting installs in PHP 8+ environments and will do more testing in that arena, so keep checking for updates.  There might be forthcoming features added to the export library.  However, the code here is largely working as intended.  The GUI has resisted needing any user controlled settings, but as testing continues this might change.  The need for a search feature is being evaluated.