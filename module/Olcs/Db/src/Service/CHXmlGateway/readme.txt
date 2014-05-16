To use this, you need to set up database (or rewrite CHXmlGateway class in lib folder)
To set up database just run the .sql script in __sql folder,
everything is prepared. If you used companies house gateway account before
you need to change autoincrement id in the request table to the last 
transactionID you used.

You can find diagram for database inside __sql folder

Class diagram is inside __doc folder and documentation is in
__doc/html/index.html

Before you start, you need to set password (line 47), senderID (line 57)
and database (line 73-76)

The next step is to check example files (in this folder).

After this, everything should be clear. If you have any questions, or something
is not working properly, drop me an email - there's no other way to find out.
