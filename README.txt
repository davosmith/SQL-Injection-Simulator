This is intended as an example to be used in a lesson to simply (and safely) demonstrate a common way of 'hacking' into a website - SQL Injection ( http://en.wikipedia.org/wiki/SQL_injection )

As you attempt to log in to the site

You need to host the file 'index.php' on a server (with PHP enabled).

The login details for the 3 fake accounts are stored inside a file.

You can simulate an SQL injection with the username: ' OR '1'='1
(make sure all 4 ' symbols are included).

It should also work fine with anything else substituted for the number 1 - as long as they both match.

If you think you can improve on this, please feel free to do so.

If you want to get in contact with me, please visit http://www.davodev.co.uk/contact

