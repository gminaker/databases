TODO: 

Everyone should be able to view the website by installing a web server (ie. apache)
and pointing their browsers to the index.php file in the project. 

Everyone will need to locally enable mysql database, create the database in the 
workbench using the amsstore.sql file, and enter their credentials into the index.php
file that will allow them to connect to the database. 

The customer registration page is currently working to add users into the database. 
Use this as a starting point for other pages, and try to understand what is happening. 
I have commented customer_reg.php (in the /views/customer/ directory) as best I can. 

We still need to finish the implementation of all other files in the /views/ directory. 
Reference the customer_reg file and the boobiz_mysql file to help complete the rest of the 
pages. 

Specific things TODO:

- checkValues - for most operations
- sales_report - Started, not completed/tested (errors, doesn't work, etc.)
- process_delivery - Started, not completed/tested (errors, doesn't work, etc.)
- purchase_items - Started, not completed/tested (errors, doesn't work, etc.)
- process_refund - Not started!
- login process - actually needs to check db, etc.
- test all operations, add appropriate error messages, check for null entries (""), check inputs
- Check prev phases to make sure constraints are being checked, etc.