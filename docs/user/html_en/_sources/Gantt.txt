.. include:: ImageReplacement.txt

.. title:: Gantt charts

.. raw:: latex

    \newpage

Gantt charts
============

.. contents:: Gantt charts
   :depth: 1
   :local: 
   :backlinks: top

.. index:: ! Gantt chart (Planning)

.. _gantt-planning:

Planning
--------

This screen allows to define project planning and follow progress.

It is composed with two areas:

* :ref:`task-list-area` |one| 
* :ref:`gantt-chart-view` |two|

.. note::

   * This screen offers many features that will be described in the next sections.

.. figure:: /images/GUI/SCR_GanttPlanning.png
   :alt: Gantt (Planning)
   :align: center

   Gantt (Planning)

.. rubric:: 3 - Activity planning calculation

* Click on  |calculatePlanning| to start the activity planning calculation.  (See: :ref:`project-planning`)

.. raw:: latex

    \newpage

.. rubric:: 4 - Buttons

* Click on |storePlannedDates| to store planned dates. (See: :ref:`project-planning`)
* Click on |buttonIconPrint| to get a printable version of the Gantt chart.
* Click on |buttonIconPdf| to export Gantt chart in PDF format. (See: :ref:`export-Gantt-PDF`) 
* Click on |msProject| to export planning to MS-Project xml format.
* Click on |buttonIconColumn| to define the columns of progress data that will be displayed. (See: :ref:`progress-data-view`)
* Click on |createNewItem| to create a new item. (See: :ref:`project-planning`)


.. raw:: latex

    \newpage

.. _task-list-area:

Task list area
^^^^^^^^^^^^^^

The task list area is composed with:

* :ref:`task-list` |one| 
* :ref:`progress-data-view` |two|

.. figure:: /images/GUI/ZONE_GanttTaskListProgressData.png
   :alt: Task list & progress data view
   :align: center

   Task list & progress data view


.. rubric:: 3 - Area splitter

The splitter is used to show or hide the progress data view.

.. note::

   * The progress data view is hidden by default.
   * Move the splitter on your right to display them.

.. raw:: latex

    \newpage

.. _task-list:

Task list
"""""""""

The task list displayed planning elements in hierarchical form.

Tasks are regrouped by project and activity.

.. rubric:: Projects displayed

* Projects displayed depends on selection done with the project selector.
* See: :ref:`top-bar`



.. figure:: /images/GUI/ZONE_GanttTaskList.png
   :alt: Task list zone
   :align: center

   Task list zone

.. rubric:: 1 - Hierarchical level

* Click on |minusButton| or |plusButton| to adjust the hierarchical level displayed.

.. rubric:: 2 - Icon of element

* An icon is displayed on the left of the corresponding element.

.. rubric:: 3 - Group row

* Click on |minusButton| or |plusButton| on the group row to expand or shrink the group.


.. rubric:: 4 - Show WBS

* Click on "Show WBS" to display the WBS number before the names.

.. rubric:: 5 - Item name 

* Click on a row will display the detail of the item in the detail window.

.. rubric:: 6 - Checkbox «Show closed items»

* Flag on «Show closed items» allows to list also closed items.

.. raw:: latex

    \newpage

.. _progress-data-view:

Progress data view
""""""""""""""""""

The progress data view allows to show progress on project elements.

For each planning element, the progress data are displayed at them right.


.. figure:: /images/GUI/ZONE_GanttProgressData.png
   :alt: Progress data view
   :align: center

   Progress data view

.. rubric:: 1 - Group row

* The group row has a gray background.
* Used to display consolidated progress data for tasks.

.. rubric:: 2 - Task row

* The task row has a white background.
* Used to display task progress data.

.. raw:: latex

    \newpage

.. rubric:: 3 -  Define the columns of progress data that will be displayed

* Click on |buttonIconColumn| to define the columns displayed.
* Click on **OK** button to apply changes.

 .. compound:: **Column selection**

    * Use checkboxes to select or unselect columns to display.

 .. compound:: **Columns order**

    * Use the |buttonIconDrag| to reorder columns with drag & drop feature.
   

.. figure:: /images/GUI/TIP_GanttSelectColunmsToDisplay.png
   :alt: Popup list - Select columns
   :align: center

   Popup list - Select columns

.. raw:: latex

    \newpage

.. _gantt-chart-view:

Gantt chart view
^^^^^^^^^^^^^^^^

The Gantt chart view is a graphic representation of progress data.

For each planning element, a Gantt bar is displayed at them right.

.. figure:: /images/GUI/ZONE_GanttChartView.png
   :alt: Gantt chart view
   :align: center

   Gantt chart view

.. rubric:: 1 - Scale

* Scale available: daily, weekly, monthly or quarter
* The Gantt chart view will be adjusted according to scale selected.

.. rubric:: 2 - Start and end dates 

* Change the starting or ending date to limit the display of Gantt chart view.

.. rubric:: 3 - Saving dates

* Save previous dates to retrieve them on every connection.

.. raw:: latex

    \newpage

.. rubric:: 4- Gantt bars

* Overdue tasks appear in red, others in green.

 .. compound:: **Red bar**

   .. describe:: Condition

      Planned end date or (Real end date if completed task)  > Validated end date

 .. compound:: **Purple bar** 

    * The planning calculator tries to plan, the remaining work on the task assigned to a resource within the project affection period.
    * If remaining work on the task can't be planned, a purple bar appears in the Gantt view.


 .. compound:: **Consolidation bar**

    .. image:: /images/ganttConsolidationBar.png
       :alt: consolidation bar

    * Displayed at group row level.
    * Graphic display of consolidated dates for planning elements group.
    * Start with the smallest start date and end with the biggest end date, either with planned or real dates.


  .. compound:: **Real work progress**

    .. image:: /images/ganttGreenBar.png
       :alt: green bar

    * The line that cross a Gantt bar displays the percentage of actual progress.
    * The length of the line represents the percentage of completion, based on the percentage of actual progress against the length of Gantt bar.


 .. note:: 

    * Move the cursor over the bar to display item name and planned dates.

.. rubric:: 5 - Dependency links

* Dependencies between planning elements are displayed with an arrow.

.. raw:: latex

    \newpage

.. rubric:: 6 - Milestone

* Milestones appear as diamonds, filled if completed, empty if not.
* Color of  diamond depends on milestone progress.

  .. compound:: **Ongoing milestone and in times**

   .. image:: /images/ganttGreenMilestone.png
      :alt: ongoing milestone and in times

  .. compound:: **Completed milestone and in times**

   .. image:: /images/ganttFilledGreenMilestone.png
      :alt: completed milestone and in times

  .. compound:: **Ongoing milestone and delayed**

   .. image:: /images/ganttRedMilestone.png
      :alt: ongoing milestone and delayed

   .. describe:: Condition

      Planned end date > Validate end date

  .. compound:: **Completed milestone and delayed**

   .. image:: /images/ganttFilledRedMilestone.png
      :alt: completed milestone and delayed

   .. describe:: Condition

      Real end date > Validated end date




.. rubric:: 7 - Show resources 

* Click on “Show resources” to display resources assigned to tasks.

.. topic:: Global parameter “Show resource in Gantt”

   * This parameter defines the option availability and whether the resource name or initial is displayed.

.. rubric:: 8 - Current date

* Yellow column indicates the current day, week, month or quarter, according to scale selected.

.. raw:: latex

    \newpage

.. index:: ! Project planning

.. _project-planning:

Project planning
^^^^^^^^^^^^^^^^

Project planning and activity planning calculation can be done in the Gantt.



.. figure:: /images/GUI/SCR_GanttPlanningProject.png
   :alt: Project planning
   :align: center

   Project planning 

.. rubric:: 1 - Add a new planning element

* Allows to create a new planning element.
* The created element is added in the Gantt and detail window is opened.
* The detail window allows to complete entry.

.. figure:: /images/GUI/TIP_CreateNewItem.png
   :alt: Popup menu - Create a new item
   :align: center


   Popup menu - Create a new item

.. note:: Planning elements management
  
   * Planning elements can be managed with their own dedicated screen.
   * Test session and Meeting elements can be added to the planning with their own dedicated screen.  

.. rubric:: 2 - Reorder planning elements

* The selector |buttonIconDrag| allows to reorder the planning elements.


.. rubric:: 3 - Indenting element

* Click on an element, the detail window will be displayed.
* Two new buttons are displayed in the header, they allow to increase or decrease indent of an element.

 .. compound:: **Increase indent**

    * The element will become the child of the previous element.

 .. compound:: **Decrease indent**

    * The element will be moved at the same level than the previous element.


.. rubric:: 4 - Dependency links

* To create a dependency link, clicked and hold on a graphic element, the mouse cursor changes to |dndLink|.
* Move mouse cursor on graphic element that will be linked and release the button.

 .. note:: Dependency links management
  
    * Dependency links can be managed in element screen. 
    * See: :ref:`predSuces-element-section`.


.. rubric:: 5 - Activity planning calculation

* Click on |calculatePlanning| to start the activity planning calculation.

 .. compound:: **Automatic run plan**

    * Check the box to activate automatic calculation on each change.

.. raw:: latex

    \newpage

.. rubric:: 6 - Store planned dates

* Allows to store planned dates into requested and validated dates.
* In other words, this feature allows to set baseline dates and preliminary dates from calculated planning.

 .. compound:: **Action available**

    * **Always:** Always overwrite existing values.
    * **If empty:** Store only if the value is empty.
    * **Never:** Values are not stored.
  

.. figure:: /images/GUI/BOX_StorePlannedDates.png
   :alt: Dialog box - Store planned dates
   :align: center


.. raw:: latex

    \newpage


.. index:: ! Gantt chart (Projects portfolio)

Projects portfolio
------------------

This screen displays Gantt chart from projects portfolio point of view.

It displays projects synthesis and project's dependencies, without project activities.

.. note::

   * This section describes specific behavior for this screen.
   * All others behaviors are similar to :ref:`gantt-planning` screen.


.. figure:: /images/GUI/SCR_GanttProjectsPortfolio.png
   :alt: Gantt (Projects portfolio)
   :align: center

   Gantt (Projects portfolio) 

.. rubric:: 1 - Show milestones

* It is possible to define whether milestones are displayed or not.
* If they are displayed, then It is possible to define the type of milestone to be displayed or displayed all. 



.. raw:: latex

    \newpage

.. index:: ! Gantt chart (Resource planning)

Resource Planning
-----------------

This screen displays Gantt chart from the resources point of view.

Assigned tasks are grouped under resource level.

.. rubric:: Gantt bars

* For activities, the Gantt bar is split in two: 

  * Real work in grey.
  * Reassessed work in green.

 .. hint::

    * This makes appear some planning gap between started work and reassessed work.

.. rubric:: Dependencies behavior

* Links between activities are displayed only in the resource group. 
* Links existing between tasks on different resources are not displayed.

.. note::

   * This section describes specific behavior for this screen.
   * All others behaviors are similar to :ref:`gantt-planning` screen.


.. figure:: /images/GUI/SCR_GanttResourcePlanning.png
   :alt: Gantt (Resource planning) 
   :align: center

   Gantt (Resource planning)


.. rubric:: 1 - Show project level

* Tasks can be grouped by project.
* Click on “Show project level” to display project level.

.. rubric:: 2 - Show left work

* Left work can be displayed at right from Gantt bar.
* Click on “Show left work” to display left work for each item.

.. raw:: latex

    \newpage

.. _export-Gantt-PDF:

Export planning to PDF
----------------------

Allows to export planning to PDF format.

Export contains all details and links between tasks.

.. figure:: /images/GUI/BOX_ExportPlanningPDF.png
   :alt: Dialog box - Export planning to PDF
   :align: center


.. tabularcolumns:: |l|l|

.. list-table:: Fields - Export planning to PDF dialog box
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Orientation
     - Page orientation.
   * - Zoom
     - Allows to fit planning on page.
   * - Repeat headers
     - Planning can be  span multiple pages.

.. note:: Technical points

   * This new feature will execute export on client side, in your browser.
   * Thus the server will not be *heavy loaded* like *standard* PDF export does.
   * It is highly faster than *standard* PDF export.
   * Therefore, this feature is hightly dependant to browser compatibility.
   
.. note:: Browser compatibility

   * This new feature is technically complex and it is not compatible with all browsers.
   * Enabled only with Chrome browser as of now.
   * Else, the old export feature will be used.

.. note:: Forced feature activation (deactivation)

   * To enable this feature for all browsers, add the parameter **$pdfPlanningBeta='true';** in parameters.php file.
   * To disable if for all brosers (including Chrome), add the parameter **$pdfPlanningBeta='false';**
   * Default (when **$pdfPlanningBeta** parameter is not set) is *enabled with Chrome, disabled with other browsers* 

