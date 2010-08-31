.. -*- coding: utf-8 -*-

===================================
eZXMLExport extension documentation
===================================

-------------------
Translations export
-------------------

:Date: 2010/08/31

.. contents:: Table of contents

Installation
============

Updating your SQL schema
------------------------
If you installed this extension in version > 1.1, you will need to update your SQL schema

::

    mysql -u<user> -p<password> -h<host> <database_name> < extension/ezxmlexport/update/database/mysql/1.2.0/dbupdate-1.1.0-to-1.2.0.sql
    
Regenerating the autoload array
-------------------------------
You should run the following command after that :

::

    php bin/php/ezpgenerateautoloads.php -e -p
    
Using the extension
===================

Once installed, you will now be available to choose which translations to export.
Note that if you don't select any translation, all available translations will be exported.

Using the cronjob
=================
This extension is now shipped with another cronjob, **ezxmlexporttranslations**
Defining the correct schedule is up to you.

The only line you have to use is the following

::

    php runcronjobs.php ezxmlexporttranslations

You can still use the old *ezxmlexport* cronjob. The new one will allow to export translations
