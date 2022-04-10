The Hoopla PHP Framework
===============================

The **Hoopla PHP Framework** is a low-level programming framework and environment (HTML IDE) designed to meta-contextualize web application code when written primarily in PHP, HTML, Javascript, CSS and SQL.

Put another way, it organizes code objects by using both an HTML-based IDE and a back-end MySQL database.

The addition of an IDE and database back-end enables web application developers to use the framework without a lot of training.

Once the application is developed, the back-end database is published along with user created code to the final production server.

Exported classes written in PHP allow the published code access to the back-end database without needing the IDE code.

The level of code abstraction is such that several powerful features are available:

1. 	Object reuse with page values and/or arbitrary context values.
2.  Page spanning object set recall with contextual values.
3.  Pages work with templates, normally many-to-one, though many-to-many is conceivable.
4.  All objects and values are organized by a type system.
5.  The type system can be expanded or modified within the IDE.
6.  The exported functions are easy to learn.



Installation	
===============================

Please consult the INSTALLATION file for more details.

The following steps serve as an outline:

1.  Set up a local PHP and MySQL web (HTTP) server for installing the framework IDE and developing the web application.
2.  Use the MySQL install scripts to set up the Hoopla database and users.
3.  Install the Hoopla Web IDE pages in a local web folder.
4.  Make sure the Hoopla Web IDE pages can connect with the local Hoopla database.
5.  Create your web application project in a separate web folder.
6.  Install the exported Hoopla classes to your project class folder (however your paths are determined).
7.  Make sure your application pages can connect with the Hoopla database.



Help	
===============================

The IDE comes with extensive help pages on how to use the framework.  There should also be a sample application you can get from our repo.



License	
===============================

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

