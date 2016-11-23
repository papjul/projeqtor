.. include:: ImageReplacement.txt

.. raw:: latex

    \newpage

.. title:: Today (Dashboard)


.. index:: ! Today
.. index:: ! Dashboard

Today (Dashboard)
=================

This screen allows user to have a global view of its projects and tasks.

.. contents:: Sections
   :local: 
   :backlinks: top

.. note:: User parameter “First page”

   * This parameter defines the screen that will be displayed first on each connection.
   * By default, this screen is selected.

.. index:: ! Message (Section)

.. _messages-section:

Messages
--------

.. figure:: /images/GUI/SEC_TodayMessages.png
   :alt: Section - Messages 
   :align: center


.. rubric:: Messages

* Messages are displayed  depends on some criteria. 
* Every message is component by title |one| and message |two|.
* Messages are defined in :ref:`message` screen.


.. rubric:: Automatic refresh

* Allows to refresh data according defined delay. 
* Also the screen will be scrolling from top to bottom according defined delay.
* Click on |buttonIconRefresh| to enable/disable automatic refresh. 


.. rubric:: Print

* Click on |iconPrint| to print Today screen. 


.. raw:: latex

    \newpage

.. rubric:: Parameters

* Click on |buttonIconParameter| to access screen parameters.

 .. compound:: **Period for task selection**

    * Allows to define the period for tasks will be displayed.

      .. compound:: **Field: Due date**

         * Select only items with due date less than today plus this selected period.

      .. compound:: **Field: Or not set**
 
         * Select also items with due date not set. 

 .. compound:: **Refresh parameters**

    * Allows to define parameters for automatic refresh.

      .. compound:: **Field: Refresh delay**
         
         * Selects the delay between two screen refresh.

      .. compound:: **Field: Scroll delay**
         
         * Selects the delay between two scrolling.

 
 .. compound:: **Items to be displayed**

    * Allows to define sections displayed on the screen.
    * Allows to reorder sections displayed with drag & drop feature.
    * Using the selector area button icon drag |buttonIconDrag|. 

.. figure:: /images/GUI/BOX_TodayParameters.png
   :alt: Dialog box - Today parameters 
   :align: center



.. raw:: latex

    \newpage


.. index:: ! Start guide


Start guide
-----------

* Start page for new installations to assist the administrator in the first configuration steps.
* A progress display |one| allows to determine the percent of complete installation.
* You can hide this section on startup, just unchecked. |two|

  * This section will not be displayed anymore.
  * To show it again, select it as the start page in :ref:`Users parameters<graphic-interface-behavior-section>` screen. 

.. figure:: /images/GUI/SEC_TodayStartGuide.png
   :alt: Section - Start guide 
   :align: center




.. raw:: latex

    \newpage

Projects
--------

A quick overview of projects status.

The projects list is limited to the project visibility scope of the connected user. 

.. figure:: /images/GUI/SEC_TodayProjects.png
   :alt: Section - Projects 
   :align: center



.. rubric:: 1 - Scope of the numbers counted

* Checkboxes allow to filter displayed projects:

  * To do: Projects to do.
  * Not closed : Projects to do and done.
  * All : Projects to do, done and closed.

.. rubric:: Projects name

* Click on the name of a project will directly move to it. 

.. rubric:: Manuel indicators

* Manuel indicator can be set on project.
* Trend and health status indicators are displayed.

 .. compound:: **2 - Icon: Trend**

    * This icon allows to display the trend of the project.

 .. compound:: **3 - Icon: Health status**

    * This icon allows to display the health status of the project.  

.. raw:: latex

    \newpage   

.. rubric:: Progress

* Calculated progress and overall progress are displayed.

 .. compound:: **4 - Calculated progress**

    * Actual progress of the work of project.

    .. note:: On mouse over the bar

       * On each project shows part of “to do” (red) compared to “done and closed” (green).

 .. compound:: **5 - Overall progress**

    * Additional progress manually selected for the project.

.. rubric:: 6 - Other measure of progress

* **Left:** Left work for the project.
* **Margin:** Work margin.
* **End date:** Planified end date of the project.
* **Late:** Number of late days in project.
 
.. rubric:: 7 - Numbers of elements concerned to project

* Numbers of elements concerned to a project are displayed.

 .. note:: On mouse over the bar

    * On each element shows part of “to do” (red) compared to “done and closed” (green).


.. raw:: latex

    \newpage

Tasks
-----

Here are listed, as a “To do list” all the items for which the connected user is either “assigned to”, “:term:`responsible` of” or “:term:`issuer` or :term:`requestor` of”.

Click on an item will directly move to it.

.. note:: Parameter: Max items to display

   * Number of items listed here are limited to a value defined  in :ref:`Global parameters<global-display-section>`.



.. tabularcolumns:: |l|l|

.. list-table:: 
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`id`
     - Unique Id for the item.
   * - Project
     - The project concerned by the item.
   * - Type
     - Type of item.
   * - Name
     - Name of the item.
   * - Due date
     - Planned end date or due date.
   * - Status
     - Actual status of the item.
   * - Issuer
     - Flag on indicate the user is the issuer for the item.
   * - Resp.
     - Flag on indicate the user is the responsible for the item.

.. topic:: Column: Id

   * Id column displayed unique Id and specific icon for the item. 


.. raw:: latex

    \newpage    

Extending
---------

You can select any report to be displayed on the Today screen.

.. rubric:: Add selected report

* To do this, just go to the selected report, select parameters and display result (to check it is what you wish on today screen). 
* Click on |buttonIconToday| to insert this report with parameter on the Today screen.
* Any unchanged parameter will be set as default value.
* These reports will be displayed on Today screen like other pre-defined parts.

.. figure:: /images/GUI/SEC_TodayExtending.png
   :alt: Report selection
   :align: center

   Report selection    

.. rubric:: Manage extending section

* Click on |buttonIconParameter| to access screen parameters.
* You can reorder like any other parts.
* Click on |buttonIconDelete| to completely remove them from the list.

.. figure:: /images/GUI/BOX_TodayParametersWithExtending.png
   :alt: Dialog box - Today parameters with extending parts 
   :align: center

   Dialog box - Today parameters with extending parts 

 


 



