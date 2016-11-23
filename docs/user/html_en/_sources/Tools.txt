.. include:: ImageReplacement.txt

.. title:: Tools

.. index:: ! Email (Sent)

.. _emails-sent:

Emails sent
-----------

Users can have a look at the list of the automatic emails sent.

All the information about the email, including the status showing whether the email was correctly sent or not.


.. index:: ! Internal alert (Sent)

.. _alerts:

Alerts
------

Users can have a look at the alerts sent.

By default, administrators can see all the alerts sent, and other users only see their own alerts.


.. figure:: /images/GUI/SCR_Alert.png
   :alt: Alert screen
   :align: center

   Alert screen


.. topic:: Button: Mark as read

   * The button is available if  the user alert is not tagged “read” yet.


.. raw:: latex

    \newpage


.. index:: ! Message


.. _message:

Messages
--------

.. sidebar:: Concepts 


   * :ref:`profiles-definition`

You can define some message that will be displayed on the :ref:`Today<messages-section>` screen of users.

Optionally, the message can be shown on login screen.

You can limit the display by profile, project and user.

The message will be displayed in a color depending on the message type.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table:: Message description section fields
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the message. 
   * - **Title**
     - Header of the message.
   * - **Message type**
     - Type of message. 
   * - Profile
     - The message is limited to users with this profile.
   * - Project
     - The message is limited to resources affected to the project.
   * - User
     - The message is limited to this user.
   * - Show on login screen
     - Show this message on login screen. 
   * - :term:`Closed`
     - Flag to indicate that the message is archived.
 
**\* Required field**

.. rubric:: Section: Message

.. tabularcolumns:: |l|l|

.. list-table:: Message message section fields
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Message<Description>`
     - Complete text of the message. 

.. raw:: latex

    \newpage


.. index:: ! Import data

.. _import-data:

Import data
-----------

Imports data from CSV or XLSX files.


.. rubric:: How to do

1. Select the element type from the list.
2. Select file format (CSV or XLSX).
3. Select the file.
4. Click on **Import data** button to start importing.

.. rubric:: Report of the import

* Data that is not imported because not recognized as a field appear in grey text in the result table.
* Data that are voluntarily not imported (because must be calculated) appear in blue text in the result table.

.. note:: Import users

   * The password field must be cut and pasted from the database because it is encrypted.
   * If you enter some readable password, the users will not be able to connect.

   .. Attention:: 

      * If you want to create new users **don't put any id** because if id already exists, it will be overridden by the new (with possibility to erase admin user…).
      * Always keep in mind that your import may have some impact on administrator user.
      * So be sure to keep an operational admin access.


.. raw:: latex

    \newpage


.. _file-format:

File format
"""""""""""

The content of the imported file must fit the element type description.

To know the data that may be imported, click on the |buttonIconHelp| button.

.. rubric:: Names of columns 

* The first line of the file must contain the name of the fields.

.. note::

   * Names of columns can contain spaces (to have better readability).
   * The spaces will be removed to get the name of the column.

.. hint:: 
   
   * Look into the model class. The names are the same.

.. rubric:: Date format

* Dates are expected in format “YYYY-MM-DD”.



.. raw:: latex

    \newpage

Data import process
"""""""""""""""""""

Operations are performed, depending on whether the element type, the column or the column value.

.. rubric:: Column Id 

You may or may not add an "id" column in the file.

 .. compound:: **Column "id" exists and "id" is set in a line** 

    * The import will try to update the corresponding element, and will fail if it does not exist.

 .. compound:: **Column "id" does not exist or if "id" is not set in a line**

    * The import will create a new element from the data.  

.. rubric:: Linked tables

For columns corresponding to linked tables ("idXxxx"), you can indicate as the column name  either "idXxxx“ or “Xxxx" (without "id") or the caption of the column (as displayed on screens).

 .. compound:: **Numeric value**

    * If the value of the column is numeric, it is considered as the code of the item.

 .. compound:: **Non numeric value**

    * If the value of the column contains non numeric value, it is considered as the name of the item, and the code will be searched for the name. 

.. rubric:: **Columns with no data**

* In any case, columns with no data will not be updated.
* Then you can update only one field on an element.

.. rubric:: **Clear data**

* To clear a data, enter the value "NULL" (not case sensitive).


.. rubric:: Planning elements

* Insertion into "Planning" elements (activity, project), automatically inserts an element in the table “PlanningElement”.
* The data of this table can be inserted into the import file.


.. raw:: latex

    \newpage

.. index:: ! Import data (Automatic)

Automatic import
----------------

Imports can be automated.

Files placed on a defined directory will automatically be imported.

.. note:: 

   * Automatic import parameters must be set in :ref:`Global parameters<automatic-import>` screen.
   * Background task must be started by :ref:`admin-console` screen.


------------
 
The files must respect some basic rules.  

.. rubric:: File name format

* File name format is : "Class"_"Timestamp"."ext"
* Example of import file name: Ticket_20131231_235959.csv

 .. compound:: **Class**

    * The type of item to be imported (Ticket, Activity, Question, …).

 .. compound:: **Timestamp**

    * Timestamp defined to be able to store several files in the directory.
    * Format is free.
    * The recommended format is “YYYYMMDD_HHMMSS”.

 .. compound:: **Ext**

    * File extension, representing its format.
    * Valid extensions are CSV and XLSX.

.. rubric:: File format

* The files must follow the ProjeQtOr :ref:`file-format`.
* Files must be full and consistent.

   .. hint::

      * The files should not be directly created in the import folder.
      * They must be created in a temporary folder and moved afterwards. 

--------------

.. rubric:: Import process

* Correctly imported files are moved to a “done” sub folder of the import folder.
* If an error occurs during import of a file, the full file is moved to “error” sub-folder of the import folder, even if there is only one error over many other items correctly integrated.
* You can get the result as a log file and/or email summary. 

