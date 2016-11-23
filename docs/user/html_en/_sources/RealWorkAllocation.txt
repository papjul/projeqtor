.. include:: ImageReplacement.txt

.. raw:: latex

    \newpage



.. title:: Real work allocation

.. index:: ! Real work allocation 

.. _RWA:

Real work allocation
--------------------

This screen is devoted to input of real work.

Resource enters work day by day, on each assigned task.

Data entry for one resource, on a weekly base.

.. note::

   * The corresponding cost to the real work is automatically updated to the assignment, activity and project.

.. important:: **Global parameter: Number of hours per day**

   * In global parameters screen, you can define wether work will be entered in hours or in days.
   * If you enter work in hours, you must define  the parameter **number of hours per day** before some real work has been entered.
   * After first work is entered, this parameter will be locked.

.. figure:: /images/GUI/SCR_TimeSheetZone.png
   :alt: Timesheet zone screen
   :align: center

   Timesheet zone screen

.. rubric:: 1- Selection timesheet

* Allows to select a timesheet for a resource and for a period.
* More detail about selection timesheet, see : :ref:`selectionTimesheet-section`.

.. rubric:: 2 - Show planned work

* Check this box to display the planned work.
* Planned work is indicated over each input cell, on top right corner, in light blue color.
* Allows to display the planned working time by day, for the resource assigned to the task.

.. figure:: /images/GUI/ZONE_RealWorkAllocationWithPlannedWork.png
   :alt: Planned work displayed zone
   :align: center

   Planned work displayed zone

.. note::

   * The planned work will be deleted when the real work is entered (to avoid duplication of work in reports).

.. rubric:: 3 - Filters

* Filters allow to define the list of assigned tasks displayed.

 .. compound:: **Show only current week meeting**

    * Check this box  to show only the tasks on meetings of the week.

 .. compound:: **Hide not handled items**

    * Check this box to hide tasks not taken over.

 .. compound:: **Hide done items**

    * Check this box to hide completed tasks.

 .. compound:: **Show closed items**

    * Check this box to show closed tasks.

.. note:: Global parameter "Display only handled tasks"

   * If the value of the parameter is set to "Yes", only tasks taken over (status "handled") will be displayed.
   * The checkbox "Hide not handled items" will be hidden.

.. rubric:: 4 - Buttons

Buttons of the timesheet:

* Click on |buttonIconSave| to save timesheet data.
* Click on |buttonIconPrint| to print timesheet.
* Click on |buttonIconPdf| to export timesheet in PDF format.
* Click on |buttonIconUndo| to undo modification on the timesheet.

.. rubric:: 5 - Data entry validation

Buttons allow to send and validate real work.

 .. compound:: **Button: Submit work**

    * Users can send works to project leader.

 .. compound:: **Button: Validate work**

    * Project leaders can validate works.


.. rubric:: 6 - Scroll bar

* Scroll bar allows the scrolling on imputation lines.
* The header of table stays visible.
* The footer of the table (with the sum of inputs) remains visible, fixed, as soon as the number of lines is greater than 20.

.. rubric:: 7 - Input fields

* Input fields in timesheet.
* More detail about, see : :ref:`inputFields-section`


.. raw:: latex

    \newpage

.. rubric:: 8 - Tasks list

.. figure:: /images/GUI/ZONE_TaskList.png
   :alt: Task list zone
   :align: center

   Task list zone

The list displays the assigned tasks for the resource.

* Only assigned tasks that meet the next criteria will be displayed.
   
  * Assigned tasks planned during this period.
  * Assigned tasks that meet the criteria of selected filters.

  .. note::

     * Assigned tasks with real work are always displayed, even if closed.
     * The goal is to show all lines of the sum for each column, to be able to check that the week is completely entered.


 .. compound:: **A - Tasks**

    * Assigned tasks are grouped by project and displayed according the project structure.  
    * Click on the name of the activity to access it.  
    * Click on |minusButton| or |plusButton| on the line will expand-shrink the group.
    * Click on the icon of the activity to display it detail without leaving the current screen.  
 

 .. compound:: **B - Assigned task function**

    * The assigned task function is displayed in blue after the name of the activity.  

 .. compound:: **C - Assigned task comments**

    * The icon |Note| indicates there is a comment on assigned task. 
    * Just move the mouse over the icon to see comments.

 .. compound:: **D - Progress data**

    * **Planned dates**: Planned start and end dates.
    * **Assigned**: Planned work assigned to the resource.
    * **Real**: Sum of work done by the resource.
    * **Left**: The remaining planned work. 
    * **Reassessed**: The work needed to complete the task.  




.. raw:: latex

    \newpage

.. _selectionTimesheet-section:

Selection timesheet
^^^^^^^^^^^^^^^^^^^

.. figure:: /images/GUI/ZONE_TimeSheetSelector.png
   :alt: Timesheet selector zone
   :align: center

   Timesheet selector zone

.. rubric:: 1 - Selection of the resource 

* Users can only select themselves as a resource.

 .. note:: Access to other resources timesheet

    * Depending on access rights, user can select other resource timesheet. 

.. rubric:: Selection period

* By default, the period is determined depending on the current day.
* It is possible to change the period of two ways:

  * Select year and week. |two|
  * Or select the first day of the week. |three| 

.. rubric:: Displayed timesheet

* A timesheet is displayed depends on the resource and period selection.
* The name of the resource and the week are displayed. |four|
* The days of the week are displayed. |five|
* The current day is displayed. |six|



.. raw:: latex

    \newpage

.. _inputFields-section:

Input fields
^^^^^^^^^^^^

.. figure:: /images/GUI/ZONE_InputTimeSheet.png
   :alt: Input timesheet zone
   :align: center

   Input timesheet zone

.. rubric:: 1 - Comments

* A global comment can be added on the weekly follow-up.

.. rubric:: 2 - Real work entry

* Area allows to entry real work.
* Week is displayed from monday to sunday.
* It possible put real work in off days.

 .. compound:: **Unit time of real work data entry**

    * The global parameter "Unit for real work allocation" allows to set the unit time.
    * Unit time available are in "Days" or "Hours".
    * Selected unit time is displayed on left at bottom window. |four|      

 .. compound:: **Current day**

    * Columns of current day is displayed in yellow.

 .. compound:: **Days off**

    * Columns of days off is displayed in grey.
    * Days off is determine in resource calendar definition, see: :ref:`calendar` screen.

.. rubric:: 3 - Left work

* Left work is automatically decreased on input of real work.
* Resources can adjust this value to estimate the work needed to complete the task.



.. raw:: latex

    \newpage


Input entry validation
^^^^^^^^^^^^^^^^^^^^^^

This section explains controls done on data entries.

These controls are not blocking.

.. rubric:: Resource capacity validation


* The total of the day is green whether entries respects the resource capacity of days.
* The total of the day is red whether entries is more than the resource capacity of days.

.. topic:: Resource capacity of days

   * The resource capacity is defined by the number of hours per day and the resource capacity.
   * The number of hours per day is defined in :ref:`Global parameters<unitForWork-section>` screen.
   * The capacity of the resource is defined :ref:`resource` screen.


.. figure:: /images/GUI/ZONE_RealWorkAllocationWithColumnsValidation.png
   :alt: Columns validation zone
   :align: center

   Columns validation zone


.. rubric:: Entering real work is in excess of the number of days specified 

* This alert box appears when the real work to a resource is entering  ahead of time.
* The number of days in advance, resource can enter his real work is defined in "max days to book work" parameter in :ref:`Global parameters<unitForWork-section>` screen.

.. figure:: /images/GUI/ALERT_RealWorkOverExpectedDays.png
   :alt: Real work over expected days alert
   :align: center

   Real work over expected days alert


.. raw:: latex

    \newpage

Automatically changing the status of tasks
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The task status can be changed automatically according to data entries on real work and left work.

.. rubric:: Global parameter "Set to first 'handled' status"

* If the parameter value is set to "Yes", when real work is entered on a task, its status will be changed automatically to the first status "handled".

.. rubric:: Global parameter "Set to first 'done' status"

* If the parameter value is set to "Yes", when left work is set to zero on a task, its status will be changed automatically to the first status "done".

.. rubric:: Change status validation

* An icon will be displayed on the task if a status change is applicable.

 .. compound:: **Icons**

    * |statusStart| Due to real work is entered, the task status will be changed to the first 'handled' status.
    * |statusStartKO| The real work is entered, but the task status will not change because at issue is occurring. 
    * |statusFinish| Due to no more left work, the task status will be changed to the first ‘done’ status.
    * |statusFinishKO| No more left work, but the task status will not change because at issue is occurring. 

    .. note::

       * Move the cursor over the icon to see the message.

 .. compound:: **Common issue**

    * If a :term:`responsible` or a :term:`result` are set as mandatory in element type definition for the task. It's impossible to set those values by real work allocation screen.
    * The change status must be done in treatment section on the task definition screen.



