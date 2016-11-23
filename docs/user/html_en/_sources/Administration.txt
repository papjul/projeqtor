.. title:: Administration

.. note::

   * The screens described below are restricted to users with administrator profile.
   * Users with others profiles can have access whether access rights is granted.   


.. index:: ! Administration console

.. _admin-console:

Administration console
----------------------

Administration console allows to execute administration tasks on application.

.. index:: ! Internal alert (Background tasks)
.. index:: ! Email (Background tasks)
.. index:: ! Import data (Background tasks)

.. rubric:: Section: Background tasks

* Allows to start and stop background task is a specific threaded treatment that regularly checks for indicators to generate corresponding alerts, warnings and automatic import when needed.


.. index:: ! Internal alert (Send)

.. rubric:: Section: Send an internal alert

* Allows to send an internal alert to users.

.. index:: ! Connection (Management)

.. rubric:: Section: Manage connections

* Allows to force disconnection of active users and close the application for new connections.

 .. compound:: **Button: Disconnect all users**

    * Allows to disconnect all connected users except your own connection.
    * The application status is displayed below.

    .. note::

       * Disconnection will be effective for each user when his browser will ckeck for alerts to be displayed.
       * The delay for the effective disconnection of users will depend on the parameter “delay (in second) to check alerts” in :ref:`Global parameters<automated-service>` screen.

 .. compound:: **Button: Open/Close application**

    * Allows to open and close application.
    * When the application is closed the message below will appear on login screen.


.. index:: ! Email (Maintenance of Data)
.. index:: ! Internal alert (Maintenance of Data)
.. index:: ! Connection (Maintenance of Data)

.. rubric:: Section: Maintenance of Data
 
* The administrator has the possibility to:

  * Close and delete sent emails and alerts. 
  * Delete history of connections. 
  * Updating references for any kind of element.

.. index:: ! Log file (Maintenance)   

.. rubric:: Section: Log files maintenance

* The administrator has the possibility to:
  
  * Delete old log files.
  * Show the list and specific log file.

.. index:: ! Audit connections
.. index:: ! Connection (Audit)

.. _audit-connections:

Audit connections
-----------------

* Audit connection proposes a view of “who is online”.

.. note::

   * The administrator has the possibility to force the disconnection of any user (except his own current connection), see: :ref:`admin-console`.

.. raw:: latex

    \newpage

.. index:: ! Global parameters

.. _global-parameters:

Global parameters
-----------------

Global parameters screen allows configuration of application settings.

.. note:: Tooltip

   * Moving the mouse over the caption of a parameter will display a tooltip with more description about the parameter.

.. _daily-work-hours-section:

.. rubric:: Section: Daily work hours

* Definition of regular “work hours”.

* Used to calculate delays based on “open hours”.



.. index:: ! Real work allocation (Unit for work)
.. index:: ! Workload (Unit form work)

.. _unitForWork-section:

.. rubric:: Section: Units for work

* The parameters to real work allocation and workload.

 .. compound:: **Fields: Unit for real work allocation and for all work data**

    * Definition of the unit can be in days or hours.

    .. note::
     
       * If both values are different, rounding errors may occur.
       * Remember that data is always stored in days.   
       * Duration will always be displayed in days, whatever the workload unit. 

 .. compound:: **Number of hours per day**

    * Allows to set number of hours per day.

 .. compound:: **Max days to book work**

    * Allows to set a max of days resource can enter real work without receiving an alert. 




.. rubric:: Section: Planning

* Specific parameters about Gantt planning presentation.

 .. compound:: **Show resource in Gantt**

    * Select if the resource can be displayed in a Gantt chart, and format for display (name or initials).

 .. compound:: **Max projects to display**

    * Defines maximum number of projects to display.
    * To avoid performance issues.

 .. compound:: **Print Gantt with 'old style' format**

    * Propose possibility to display “old style” Gantt.
    * May cause performance issues, but could fix some display issue on browsers.

 .. compound:: **Consolidate validated work & cost**

    * Select if validated work & cost are consolidated on top activities and projects :
  
      * **Never** : Not consolidate
      * **Always** : Values are replaced on activities and project.
      * **Only is set** : Replaces values, only if not already set. 

 .. compound:: **Apply strict mode for dependencies**

    * Defines if a task can begin the same day as the preceding one.
 


.. index:: ! Real work allocation (Behavior)

.. _realWorkAllocation-section:

.. rubric:: Section: Real work allocation

* Defines behavior of tasks in the real work allocation screen.

 .. compound:: **Display only handled tasks**

    * Display only tasks with "handled" status.

 .. compound:: **Set to first 'handled' status**

    * Change status of the task to the first "handled" status when  real work is entered.

 .. compound:: **Set to first 'done' status**

    * Change status of the task to the first "done" status when no left work remains.


.. _responsible-section:

.. rubric:: Section: Responsible

* Behavior about management of responsible, including automatic initialization of responsible.

 .. compound:: **Auto set responsible if single resource**

    * Automatically set responsible if not set and only one resource if affected to the project.

 .. compound:: **Auto set responsible if needed**

    * Automatically set responsible to current resource if not set and the responsible is required (depending on status).

 .. compound:: **Only responsible works on ticket**

    * Only responsible can enter some real work on the ticket.



.. _user-password-section:

.. rubric:: Section: User and password

* Security constraints about users and passwords.

.. _ldap-management-section:

.. rubric:: Section: Ldap management parameters

* Information about LDAP connection and behavior on creation of new user from LDAP connection.

.. _format-reference-numbering-section:

.. rubric:: Section: Format for reference numbering

* Allows to define reference formats for items of element, documents and bills.

 .. compound:: **Global parameters for reference formatting**

    * Prefix : can contain {PROJ} for project code, {TYPE} for type code, {YEAR} for current year and {MONTH} for current month.

 .. compound:: **Global parameters for document reference formatting**

    * format : can contain {PROJ} for project code, {TYPE} for type code, {NUM} for number as computed for reference, and {NAME} for document name.
    * Suffix : can contain {VERS} for version name.

.. rubric:: Section: Localization

* Localization and internationalization (i18n) parameters.

.. rubric:: Section: Miscellanous

Miscellaneous parameters :
 
* Auto check (or not) for existing new version of the tool (only administrator is informed);

* Separator for CSV files (on export and export);

* Memory limit for PDF generation.


.. _global-display-section:

.. rubric:: Section: Display

* Selection of graphic interface behavior and generic display parameter for users.

* Icon size are default : user can overwrite these values


.. _file-directory-section:

.. rubric:: Section: Files and Directories

Definition of directories and other parameters used for Files management.

.. warning:: Attachments Directory

   Should be set out of web reach.

.. warning:: Temporary directory for reports
  
   Must be kept in web reach.

.. _document-section:

.. rubric:: Section: Document

Definition of directories and other parameters used for Documents management.

.. warning:: Root directory for documents

   Should be set out of web reach. 

-----------------------

.. _automated-service:

.. rubric:: Section: Management of automated service (CRON)

Parameters for the “Cron” process.

.. topic:: Defined frequency for these automatic functions

   * It will manage :

     * Alert generation : Frequency for recalculation of indicators values.

     * Check alert : Frequency for client side browser to check if alert has to be displayed.

     * Import : Automatic import parameters.

   .. warning:: Cron working directory

      Should be set out of web reach.

   .. warning:: Directory of automated integration files
     
      Should must be set out of web reach.

.. topic:: Defined parameters for the “Reply to” process
   
   * It will manage connection to IMAP INBOX to retrieve email answers.

   .. note:: Email input check cron delay

      * Delay of -1 deactivates this functionality. 

   .. note:: IMAP host

      * Must be an IMAP connection string.
   
      * Ex: to connect to GMAIL input box, host must be: {imap.gmail.com:993/imap/ssl}INBOX

.. _automatic-import:

.. rubric:: Automatic import

.. topic:: Field: Automatic import cron delay

   *

.. topic:: Field: Directory of automated integration files

   *

.. topic:: Field: Log destination

   * 

.. topic:: Field: Mailing list for logs

   *

------------------------

.. index:: ! Email (Parameters)

.. rubric:: Section: Emailing

Parameters to allow the application to send emails.


.. index:: ! Email (Formatted message)

.. _mail-titles:

.. rubric:: Section: Mail titles

* Parameters to define title of email depending on event (1).

(see: :ref:`administration-special-field-label`)

.. raw:: latex

    \newpage

.. index:: ! Special fields

.. _administration-special-field-label:

Special fields
""""""""""""""

Special fields can be used in the title and body mail to be replaced by item values :

* ${dbName} : the display name of the instance
* ${id} : id of the item
* ${item} : the class of the item (for instance "Ticket") 
* ${name} : name of the item
* ${status} : the current status of the item
* ${project} : the name of the project of the item
* ${type} : the type of the item
* ${reference} : the reference of the item
* ${externalReference} : the :term:`external reference` of the item
* ${issuer} : the name of the issuer of the item
* ${responsible}  : the name of the responsible for the item
* ${sender} : the name of the sender of email
* ${sponsor} : the name of the project sponsor
* ${projectCode} : the project code
* ${contractCode} : the contact code of project
* ${customer} : Customer of project 
* ${url} : the URL for direct access to the item
* ${login} the user name
* ${password} the user password
* ${adminMail} the email of administrator





