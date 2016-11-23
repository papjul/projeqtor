.. include:: ImageReplacement.txt


.. raw:: latex

    \newpage


.. title:: Common sections

.. _common-sections:

Common sections
===============

Some sections are displayed on almost all screens.

Those sections allows to set information or add information to an item of the element.

.. contents::
   :depth: 1
   :local: 
   :backlinks: top



Description section
-------------------

This section allows to identify items of the element.

* Information grouped under this section are:

  * :term:`Id`
  * Element type
  * Name
  * Description
  * Current situation
  * Stakeholder
  * Objective
  * Reference
  * Link


.. raw:: latex

    \newpage

Treatment section
-----------------

This section contains information about item treatment.

Depending on the element, this section  may have a different name.

* Information grouped under this section are:

  * Status and Dates
  * :term:`Responsible`
  * Link
  * Outcome
  * Comment





.. raw:: latex

    \newpage

.. index:: ! Project (Affectation)

.. _affectations-section:

Affectations section
--------------------

.. sidebar:: Concepts 

   * :ref:`profiles-definition`
   * :ref:`project-affectation`

This section allows to manage resource affectation to projects.



.. list-table:: Fields - Resource affectation list
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the affectation.
   * - Project
     - Project affected to.
   * - Resource
     - Name of the affected resource.
   * - Profile
     - Selected profile.
   * - Start date
     - Start date of affectation.
   * - End date
     - End date of affectation.
   * - Rate
     - Affectation rate for the project (%).


.. rubric:: Affectation list management

* Click on |buttonAdd| to create a new affectation. 
* Click on |buttonEdit| to update an existing affectation.
* Click on |buttonIconDelete| to delete the corresponding affectation.
* Click on |iconSwitch| to replace resource on the corresponding affectation. (See: :ref:`Replace resource on an affectation<replace-resource-affectation>`)
* The icon |closeIcon| indicates that affectation is closed.

.. note:: Direct access to information

   * From project screen, click on the resource name to go directly to the selected resource.
   * From resource screen, click the project name to go directly to the selected project.

.. _affectations-box:

.. figure:: /images/GUI/BOX_Affectation.png
   :alt: Dialog box - Affectation
   :align: center

.. tabularcolumns:: |l|l|

.. list-table:: Fields - Affectation dialog box
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - **Project**
     - Project list.
   * - **Resource**
     - Resource list.
   * - **Profile**
     - Profile list.
   * - Rate
     - Rate (in %) of the affectation to the project.
   * - Start date
     - Start date of affectation.
   * - End date
     - End date of affectation.
   * - Description
     - Complete description of the affectation.
   * - Closed
     - Flag to indicate that affectation in not active anymore, without deleting it.

**\* Required field**

.. topic:: Fields: Project & Resource
 
   * If the affectation is done on the screen «Projects», the field «resource» will be selectable.
   * If the affectation is done on the screens «Resources», «Contacts» or «Users», the field «project» will be selectable.

.. topic:: Field: Resource

   * This field can contain a list of users, resources or contacts according to which screen comes from project affectation.

.. topic:: Field: Profile

   * The user profile defined will be displayed first. 

.. topic:: Field: Rate

   * 100% means a full time affectation.

.. note::
 
   * Depending on which screen is used to manage project affectations, the behavior of fields will change. 


.. raw:: latex

    \newpage

.. _replace-resource-affectation:

.. rubric:: Replace resource on an affectation

* This feature allows to replace a resource by another.
* All tasks assigned to old resource will be transferred to the new resource with assigned and left work.

.. note:: 

   * Work done on tasks belongs to always the old resource.

.. figure:: /images/GUI/BOX_ReplaceAffectation.png
   :alt: Dialog box - Replace affectation 
   :align: center

.. tabularcolumns:: |l|l|

.. list-table:: Fields - Replace affectation dialog box
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - **Resource**
     - Resource list.
   * - Capacity (FTE)
     - The capacity of the resource selected
   * - **Profile**
     - Profile list.
   * - **Rate**
     - Rate (in %) of the affectation to the project.
   * - Start date
     - Start date of affectation.
   * - End date
     - End date of affectation.
 
**\* Required field**


.. topic:: Field: Profile

   * The user profile defined will be displayed first. 

.. topic:: Field: Rate

   * 100% means a full time affectation.


 

.. raw:: latex

    \newpage

.. _assignment-section:

Assignment section
------------------

.. sidebar:: Concepts 

   * :ref:`resource-function-cost`

This section allows to manage assignment of resources to tasks.

.. note::

   * Only resources affected to a project can be assigned to its tasks.

.. rubric:: Assignment function

* The assignment function allows to define the resource function to task and the daily cost if defined. 

.. rubric:: Assignment rate

* Assignment rate is used to keep some scheduling time for other tasks.
* For instance, if rate is 50%, the resource will not be planned more than half days on the task.

.. rubric:: Multiple assignment to a task

* A resource can be assigned more than once to a task.
* Allows to assign the resource to the same task, but with a different function (different daily cost).
* Allows to add extra work without modifying initial assignment.

.. rubric:: Incomplete planned work

* The scheduling process tries to schedule, the remaining work on the assigned task within the project affection period.
* The remaining work that can't be planned is displayed on the right of the resource name.

.. figure:: /images/GUI/ZONE_AssignmentSectionWithNotPlannedWork.png
   :alt: Assignment section with incomplete planned work
   :align: center

   Assignment section with incomplete planned work








.. raw:: latex

    \newpage


.. rubric:: Assignment list

.. list-table:: Fields of assignment list
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Resource
     - Name of the resource assigned to the task.
   * - Rate (%)
     - Rate planned for this resource to the task.
   * - Assigned
     - The work initially planned for this resource to the task.
   * - Real
     - Sum of work done by this resource to the task.
   * - Left
     - Remaining work to this resource to complete the task.

.. note::
 
   * Click on the resource name to directly move to resource detail.

.. rubric:: **Assignment list buttons**

* Click on |buttonAdd| to assign a new resource.
* Click on |buttonEdit| to modify the assignment.
* Click on |buttonIconDelete| to delete the assignment.

.. note::

   * If real work exists for an assignment, it can not be deleted.

.. figure:: /images/GUI/BOX_Assignment.png
   :alt: Dialog box - Assignment 
   :align: center


.. tabularcolumns:: |l|l|

.. list-table:: Fields - Assignment dialog box
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Resource
     - Resource list.
   * - Function
     - Function list.
   * - Cost
     -  Daily cost defined for the resource and its function.
   * - Rate
     - The max rate (%) to schedule the resource on the task by day.
   * - Assigned work
     - The work initially planned for this resource to the task.
   * - Real work
     - Sum of work done by this resource to the task.
   * - Left work
     - Remaining work to this resource to complete the task.
   * - Reassessed work
     - The new total work planned to complete the task.
   * - Comments
     - Any comment on the affectation.

.. topic:: Field: Function

   * The main function defined for the resource is the default function selected.

.. topic:: Field: Left work

   * [Left work] = [Assigned Work] – [Real Work]
   * Project leaders can adjust this value to estimate the work needed to complete the task by the resource.

.. topic:: Field: Reassessed work

   * [Reassessed work] = [Real Work] + [Left Work]

.. topic:: Field: Comments

   * When a comment exists, |note| will appear on the assignment list, and on the description of the activity on the “real work allocation” screen.
   * Moving the mouse over the icon will display the comment.  


.. raw:: latex

    \newpage

.. _progress-section:

Progress section
----------------

This section allows all at once, define planning and follow-up the progress.

All planning elements have a progress section.

Description of the different sections is grouped by the planning elements that have common fields and behavior.

Progress data are displayed in the same format, but according to the planning element, fields can have another meaning or behavior.

The following sections displayed first a general description of progress data.

Next, description of fields and information in sections will be displayed.

.. raw:: latex

    \newpage

.. _progress-section-column-def:

Columns definition
^^^^^^^^^^^^^^^^^^

.. rubric:: Column "Requested"

* Requested dates allows to define initial dates (wished dates).
* Useful to define preliminary dates in a project, in a change request or other.
* Requested dates can be used:
  
  * as a first draft of planned dates.
  * with indicators to monitor the respect of start and end dates.

.. rubric:: Column "Validated"

* Validated data are used by project leader to define committed dates,  work and cost expected.
* Validated data can be used:

  * to fix a limit or a target and for some planning mode they are used to define parameter entries.
  * with indicators to monitor the respect of start and end dates, work and cost expected.
  * like the project's baseline.

 .. note:: Validated end date (due date)

    * The validated end date allows to fix a due date at which the work must be completed.
    * If the work is planned or completed beyond this limit, the error field will be highlighted.
    * For task and milestone, their Gantt element will be red. (See: :ref:`gantt-chart-view`)

.. rubric:: Column "Planned"

* Planned dates are the estimated time to complete tasks or the estimated end date of milestones.
* Planned dates can be used to initialize validated and requested dates. (See: :ref:`project-planning`)

.. topic:: Columns: "planned" and "assigned"

   * Assigned work is the planned work assigned to resources for completing tasks.
   * Planned dates are the estimated duration to complete tasks by resources according to assigned work.

.. raw:: latex

    \newpage
 
.. rubric:: Column "Assigned"

* This column displays the planned work and cost of resources and the planned project expenses.

 .. compound:: **Planned work and cost of resources**

    * Scheduled work time assigned to a resource for completing a task.
    * The estimated cost is the scheduled work time assigned to a resource for completing a task multiplied by its current daily cost.

 .. compound:: **Planned project expenses**

    * Sum of the planned amount of all project expenses.

.. rubric:: Column "Real"

* This column displays the duration, completion date, work done and cost incurred by resources and the project expenses.

 .. compound:: **Dates**

    * Allows to determine when a planning element has really begun and completed. 

 .. compound:: **Work done and cost incurred by resources**

    * Work done by a resource on an assigned task.
    * Work done are entered by resources, on a daily basis by real work allocation screen. 
    * The cost incurred is the work done by a resource on a task on daily base multiplied by its daily cost defined for the period.

 .. compound:: **Project expenses**

    * Sum of the real amount of all project expenses.

.. rubric:: Column "Left"

* This column displays the remaining of planned work and planned amount.
* By default : Left = Assigned - Real
* Left work should be re-estimated by the resource.

 .. compound:: **Work and cost remaining of resources**

    * Scheduled work time assigned to resources for completing tasks minus the work done.
    * The remaining cost is the remaining work by a resource on a task multiplied by the latest daily cost defined for it.

 .. compound:: **Project expense**
 
    * Sum of the planned amount of project expenses not incurred yet.

.. rubric:: Column "Reassessed"

* This column displays the resources work reassess and the spending projection.
* Always calculated as : [Reassessed] = [Real] + [Left] 

.. raw:: latex

    \newpage

.. _progress-section-consolidation:

Work and cost consolidation
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Work and cost from tasks (Activity, Test session and Meeting) are summarized for each parent element.

Allows to follow-up progress on grouped tasks like phase, delivery, test group, sub-project and project. 

.. rubric:: Consolidate validated work and cost

* Consolidation can be done on validated work and cost.
* Allows to calculate the scheduled work and budgeted cost from lower planning elements.
* The calculation depends on consolidation method selected and on which level the validated values are entered.

 .. compound:: **Consolidation method "Never"**

    * Values are not consolidated.

 .. compound:: **Consolidation method "Always"**

    * Values on the lower levels are consolidate up to project level.

 .. compound:: **Consolidation method "Only if set"**

    * Zero values at lower levels do not overwrite values on upper level, but non-zero values on the lower level are consolidated up to project level.
 
.. note:: Selection of consolidation method

  * The parameter "Consolidated validated cost & work" in global parameters screen allows to select consolidation method.




.. raw:: latex

    \newpage

.. _progress-section-monitor-indicator:

Monitoring indicator
^^^^^^^^^^^^^^^^^^^^

The indicators can be defined on the progress data.

.. rubric:: Project, Activity and Test session.

* Next indicators defined for Project, Activity and Test session

 .. compound:: **Respect of start or end date for the requested, validated and planned values**

    * These indicators can be programmed as a reminder and an alert, according to the number of days or hours before the start or end date.

 .. compound:: **On resource work and cost**

    * These indicators can be programmed as a reminder and an alert, according to percentage of progress on monitored value compared to the target value.

     .. describe:: Reassessed cost compared to validated cost

        * The reassessed cost compared to budgeted (validated) cost.

     .. describe:: Reassessed cost compared to assigned cost

        * The reassessed cost compared to cost calculated from assigned work for resources.

     .. describe:: Reassessed work compared to validated work

        * The reassessed work compared to scheduled (validated) work.

     .. describe:: Reassessed work compared to assigned work

        * The reassessed work compared to work assigned to resources.

     .. describe:: Real work compared to validated work

        * The work really done by resources compared to scheduled (validated) work.

     .. describe:: Real work compared to assigned work

        * The work really done by resources compared to work assigned to resources.

.. rubric:: Milestone

* Next indicators defined for Milestone.

 .. compound:: **Respect of end date for the requested, validated and planned values**

    * These indicators can be programmed as a reminder and an alert, according to the number of days or hours before the end date.

.. raw:: latex

    \newpage

.. _progress-section-activity:

Progress section (Project, Activity & Test session)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Next sections describe each part of progress section display.

Some parts are used only by a specific planning element.

Parts used by planning elements are:


.. topic:: Project

   * :ref:`progress-section-date-duration`
   * :ref:`progress-section-resource`
   * :ref:`progress-section-expense-reserve`
   * :ref:`progress-section-miscellaneous`
   * :ref:`progress-section-margin`

.. topic:: Activity

   * :ref:`progress-section-date-duration`
   * :ref:`progress-section-resource`
   * :ref:`progress-section-miscellaneous`
   * :ref:`progress-section-planning-mode`
   * :ref:`progress-section-ticket`

.. topic:: Test session

   * :ref:`progress-section-date-duration`
   * :ref:`progress-section-resource`
   * :ref:`progress-section-miscellaneous`
   * :ref:`progress-section-planning-mode`


.. _progress-section-date-duration:

Dates and duration
"""""""""""""""""" 

.. only:: html

 .. sidebar:: See 

    * :ref:`progress-section-column-def`
    * :ref:`progress-section-monitor-indicator`

.. rubric:: Requested

* Allows to define preliminary planned dates.

.. rubric:: Validated

Validated dates are used to:

* Define entry parameters according to selected planning mode.
* Define initial planned dates, as a baseline.
* Fix a due date at which the work must be completed.

.. raw:: latex

    \newpage


.. rubric:: Planned

Planned dates can be defined with:

 .. compound:: **Requested or validated dates**

    * The planned dates can be initialized with validated dates or requested dates (whether validated dates are not specified).

 .. compound:: **Planning calculation**

    * The planned dates can be determined during the planning calculation.
    * The planning calculation is done according to tasks assigned to resources and their predecessors.

    .. note:: Planning mode "Fixed duration"

       * The planned dates of tasks will be calculated depending on their predecessors and their specified duration.

.. topic:: Planned dates of parent element 

   * At parent element level, dates are adjusted with the first planned start date and the last planned end date from all sub-elements.


.. note::

   * The planned start date is adjusted to the real start date when work began.

.. rubric:: Real

* The real start date is set when work began (handled).
* The real end date is set when no more remaining work (done).

.. topic:: Real dates of parent element 

   * The real start date will be propagated to parent elements up to project.
   * The real end date for parent element will be initialized, when all sub-element have been completed.

.. raw:: latex

    \newpage

.. _progress-section-resource:

Resources (Work & Cost)
""""""""""""""""""""""" 

.. only:: html

 .. sidebar:: See 

    * :ref:`progress-section-column-def`
    * :ref:`progress-section-consolidation`
    * :ref:`progress-section-monitor-indicator`


Work of resources are calculated by the work assigned to each resource for tasks.

.. rubric:: Validated

* Allows to define scheduled work and budgeted cost of resources.

 .. compound:: **Work**
 
    * This value is used for calculation of the expected progress and project margin (work).

 .. compound:: **Cost**

    * This value is used for calculation of project margin (cost).

.. note:: **Project**

   * The values of work and cost can be initialized with the sum of total work and amount of all project orders.
   * See: :ref:`order`

.. rubric:: Assigned

* Sum of planned work assigned to resources and estimated cost.

.. rubric:: Real

* Sum of work done by resources and cost incurred.

.. rubric:: Left

* Sum of estimated remaining work to complete tasks and ensuing costs.
* Left work should be re-evaluated by resource while entering the real work on real work allocation screen.
* Left work can also be changed on assignment, at project management level.

.. rubric:: Reassessed

* Sum of resource total work that will be needed from start to end and the ensuing costs.
* [Reassessed] = [Real] + [Left]

.. topic:: Work on tickets

   * Sum of work done on tickets and costs is included in work of activity linked through the “planning activity” of tickets.
   * Sum of work done on tickets that don't link to any activity will be integrated in the work of the project.

.. raw:: latex

    \newpage

.. _progress-section-expense-reserve:

Expense, Reserve and Total
""""""""""""""""""""""""""

.. only:: html

 .. sidebar:: See 

    * :ref:`progress-section-column-def`
    * :ref:`Project reserve<project-reserve>`


.. rubric:: > Used by: Project

.. rubric:: Validated (Expense)

* Allows to set the budgeted cost of project expenses.
* This value is used for calculation of project margin (cost).


.. rubric:: Assigned (Expense)

* Project expenses planned.
* Sum of "planned amount" for all expenses on project.

.. rubric:: Real (Expense)

* Project expenses committed.
* Sum of "real amount" for all expenses on project.

.. rubric:: Left (Expense)

* Project expenses not committed yet.
* Sum of "planned amount" for expenses  for which "real amount" is not defined yet.

.. rubric:: Reassessed (Expense)

* Spending projections.
* Sum of Real + Left

.. rubric:: Left (Reserve)

* Project reserve.

.. note:: Total columns

   * Total is the sum of resources cost, expenses and reserve of their corresponding column.


.. raw:: latex

    \newpage

.. _progress-section-miscellaneous:

Progress, Expected, WBS & Priority
""""""""""""""""""""""""""""""""""

.. rubric:: Progress

* Percentage of actual progress.
* Calculated by the sum of the work done divided by sum of work reassessed.
* [Progress %] = [real work] / [reassessed work] = [real work] / ( [real work] + [left work] )

.. rubric:: Expected

* Percentage of expected progress.
* Calculated by the sum of the work done divided by scheduled work.
* [Expected %] = [real work] / [validated work]

.. rubric:: WBS

* Hierarchical position in the global planning.

.. rubric:: Priority

* Allows to define priority.
* By default, the value is set to "500" (medium priority).
* See: :ref:`Scheduling priority<scheduling-priority>`.

.. raw:: latex

    \newpage

.. _progress-section-planning-mode:

Planning mode	
"""""""""""""

.. rubric:: > Used by: Activity & Test session

.. rubric:: As soon as possible

* The task is planned to finish as soon as possible.

.. rubric:: Work together

* When two or more resources are assigned to the same task, planning tries to find periods where all resources are available to work together.
* Periods are searched "as soon as possible".
* If only one resource is assigned, this planning mode is exactly the same as "As soon as possible".
* If one resource is assigned more work than the other, the extra work is planned after working together periods.

.. rubric:: Constraint by date

** The validated start or end date field must be set.

  .. compound:: **Must not start before validated date**

     * The task must not begin before a specific date.

  .. compound:: **As late as possible**

     * The task is planned backward from end to start.
     * "Floating" backward planning is not possible, validated end date must be defined. 

.. raw:: latex

    \newpage


.. rubric:: Recurrent activities

* Allows to evenly distribute work between two dates.
* Used for management recurrent activities.
* The validated dates fields must be set.

 .. note::

    * Examples of the planning modes upcoming are based on 2 work days to plan on 10 days.


 .. compound:: **Regular between dates**
 
    * Work will be evenly divided between on working days.
    * For instance, 0.2 days during 10 days.

 .. compound:: **Regular in full days**
 
    * Work will be distributed on full day between on working days.

        .. list-table::
           :widths: 10,9,9,9,9,9,9,9,9,9,9
           :header-rows: 1
           :stub-columns: 1

           * - Day
             - 1
             - 2
             - 3
             - 4
             - 5
             - 6
             - 7
             - 8
             - 9
             - 10
           * - Distribution
             - 0
             - 0
             - 0
             - 0
             - 1
             - 0
             - 0
             - 0
             - 0
             - 1

 .. compound:: **Regular in half days**

    * Work will be distributed on half of the day between on working days.

        .. list-table:: 
           :widths: 10,9,9,9,9,9,9,9,9,9,9
           :header-rows: 1
           :stub-columns: 1

           * - Day
             - 1
             - 2
             - 3
             - 4
             - 5
             - 6
             - 7
             - 8
             - 9
             - 10
           * - Distribution
             - 0
             - 0
             - 0.5
             - 0
             - 0.5
             - 0
             - 0
             - 0.5
             - 0
             - 0.5


 .. compound:: **Regular in quarter days**
 
    * Work will be distributed on one quarter of the day between on working days.

        .. list-table:: 
           :widths: 10,9,9,9,9,9,9,9,9,9,9
           :header-rows: 1
           :stub-columns: 1

           * - Day
             - 1
             - 2
             - 3
             - 4
             - 5
             - 6
             - 7
             - 8
             - 9
             - 10
           * - Distribution
             - 0
             - 0.25
             - 0.25
             - 0.25
             - 0.25
             - 0
             - 0.25
             - 0.25
             - 0.25
             - 0.25

.. rubric:: Fixed duration

* The task is planned by duration.
* The task is “floating” depending on predecessors.
* The validated duration field must be set.
* It is not necessary to assign resources to the task.
* If work is assigned to the task, planning behavior is the same as "Regular between dates" but with floating task. 

 .. note::

    * If you want fixed duration with fixed start, use "Regular between dates", or use milestone to define start.

.. raw:: latex

    \newpage

.. _progress-section-margin:

Margin
""""""

.. rubric:: > Used by: Project

Displays remaining margin.

.. note::
   
   * The percentage of remaining margins will be displayed on the right.

.. rubric:: Margin (work)
 
* Calculated by the scheduled work minus the sum of work reassessed.
* [Margin] = [Validated work] - [Reassessed work]
* [Margin (%)] = ([Validated work] - [Reassessed work]) / [Validated work] 

.. rubric:: Margin (cost)

* Calculated by the budgeted cost (resource & expense) minus the total of reassessed cost.
* [Margin] = [Validated cost] - [Reassessed cost]
* [Margin (%)] = ([Validated cost] - [Reassessed cost]) / [Validated cost] 


.. _progress-section-ticket:

Ticket
""""""

.. rubric:: > Used by: Activity

Allows tracking of tickets attached to the activity throught the "planning activity" field of tickets. 

.. tabularcolumns:: |l|l|

.. list-table:: 
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Number
     - Number of tickets attached to the activity.
   * - Estimated
     - Sum of estimated work for tickets attached to the activity.
   * - Real
     - Sum of work done for tickets attached to the activity.  
   * - Left
     - Sum of remaining work for tickets attached to the activity. 

.. topic:: Field: Estimated

   * This field will be highlighted when the sum of estimated work on the tickets is higher than the planned work on the activity.

.. topic:: Field: Left

   * This field will be highlighted when the sum of remaining work on the tickets is higher than the remaining planned work on the activity.


.. rubric:: Show tickets attached

* Click on |buttonIconSearch| to show ticket list attached to the activity.
* Click on a ticket name to directly move to it.

.. figure:: /images/GUI/BOX_ListOfTicket.png
   :alt: Dialog box - List of tickets 
   :align: center

 
.. raw:: latex

    \newpage
 	

.. _progress-section-milestone:

Progress section (Milestone)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. only:: html

 .. sidebar:: See 

    * :ref:`progress-section-column-def`
    * :ref:`progress-section-monitor-indicator`

This section allows to define planning and follow progress on a milestone.


.. rubric:: Requested

* Allows to define the initial due date for the milestone.
* Have no impact on planning.

.. rubric:: Validated

* Allows to define the due date at which the milestone must be completed.

.. _planning-mode-milestone:

.. rubric:: Planned

* Defined according to the selected planning mode.

 .. compound:: **Fixed milestone**

    * Planned due date is the value from validated due date field.
    * The milestone will not move, and may have successors.

 .. compound:: **Floating milestone**

    * Calculation of planned due date takes into account dependencies with tasks.
    * The milestone will move depending on predecessors.

.. rubric:: Real

* Determined when the status of the milestone is “done”.


.. rubric:: WBS

* Hierarchical position of the milestone in the global planning.

.. rubric:: Planning mode

* Fixed milestone
* Floating milestone

.. note::
   
   * A milestone has no duration, so there are no start and end dates for a milestone, just a single date.
   
.. raw:: latex

    \newpage


.. _progress-section-meeting:

Progress section (Meeting)
^^^^^^^^^^^^^^^^^^^^^^^^^^

.. only:: html

 .. sidebar:: See 

    * :ref:`progress-section-column-def`
    * :ref:`progress-section-consolidation`

This section allows to define priority and follow progress on a meeting.

.. rubric:: Validated

* Allows to define scheduled work and budgeted cost.
* Used to consolidate validated work and cost to the project.

.. rubric:: Assigned

* Sum of planned work assigned to attendees and the planned cost.

.. rubric:: Real

* Sum of work done by attendees and the cost.

.. rubric:: Left

* Sum of planned work remaining and the remaining amount.

.. rubric:: Priority

* Allows to define meeting priority.
* By default, the value is set to "1" (highest priority).
* See: :ref:`Scheduling priority<scheduling-priority>`.


 
.. raw:: latex

    \newpage

.. _predSuces-element-section:

Predecessor and Sucessor element sections
-----------------------------------------

.. sidebar:: Concepts 

   * :ref:`dependencies`

This section allows to manage dependency links between planning elements.

A dependency link can be created from a planning element (Predecessor) and to a planning element (Successor).

.. note:: Gantt chart

   * The dependency link can be created in the Gantt chart.
   * See: :ref:`project-planning`

.. tabularcolumns:: |l|l|

.. list-table:: Fields of predecessors and successors lists
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Element
     - Type and id of the element.
   * - Name
     - Name of the element.
   * - Status
     - Actual status of the element.

.. note::
 
   * Click on the name of a predecessor or successor will directly move to it.


.. rubric:: Predecessors and successors elements list management

* Click on |buttonAdd| on the corresponding section to add a dependency link.
* Click on |buttonEdit| to edit the dependency link.
* Click on |buttonIconDelete| to delete the corresponding dependency link. 

.. figure:: /images/GUI/BOX_PredecessorSuccessorElement.png
   :alt: Dialog box - Predecessor or Successor element
   :align: center

   Dialog box - Predecessor or Successor element



.. note:: 

   * Recursive loops are controlled on saving.

.. topic:: Linked element list of values

   * By default, the list of values shows items of the same project. But, it is possible to link items from different projects.
   * Click on |buttonIconSearch| to get the list of elements of all projects. 	

.. topic:: Multi-value selection

   * Multi-line selection is possible using :kbd:`Control` key while clicking.

.. topic:: Delay (late)

   * Days between predecessor end and successor start.



.. raw:: latex

    \newpage

.. _linkElement-section:

Linked Elements section
-----------------------

This section allows to manage link between items of elements.

.. rubric:: Used for

* Allows to associate items on different elements in the same project.
* A project can be linked with other.


.. note:: Access to an item

   * Click on an item name to directly move to it.
   * Click on |buttonIconBackNavigation| to return to the last screen. (More detail, see: :ref:`Top bar <navigation-buttons>`)


.. rubric:: Reciprocally interrelated

* If Item A is linked to Item B, Item B is automatically linked to Item A.

.. note::

   * A link between items has no impact on them treatment.
	

.. rubric:: Linked elements list

.. tabularcolumns:: |l|l|

.. list-table:: Fields of linked elements list
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Element
     - Type and id of the linked element.
   * - Name
     - Name of the linked element.
   * - Date
     - Date of creation of the link.
   * - User
     - User who created the link.
   * - Status
     - Actual status of the linked element.

.. rubric:: Linked elements list buttons

* Click |buttonAdd| to create a new link.
* Click on |buttonIconDelete| to delete the corresponding link.

.. figure:: /images/GUI/BOX_AddLink.png
   :alt: Dialog box - Add a link with element 
   :align: center

.. topic:: Linked element list of values

   * By default, the list of values shows items of the same project. But, it is possible to link items from different projects.
   * Click on |buttonIconSearch| to get the list of elements of all projects. 	




.. raw:: latex

    \newpage

.. rubric:: Link with Document

* When a link to a document is selected. The document version can be selected. (See options below)
* Linked documents are available directly in linked elements list.

 .. compound:: **Specified version**

    * A link with a document element offer the possibility to select a specific version.
    * A direct link to version of the document is created.

 .. compound:: **Not specified version**

    * If the version is not specified, the last version will be selected.
    * The download will transfer always the last version of the document.

.. raw:: latex

    \newpage

.. _attachment-section:

Attachments section
-------------------

This section allows to attach files or hyperlinks to items of elements.

.. tabularcolumns:: |l|l|

.. list-table:: Fields of attachments list
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the attachment.
   * - File
     - File name or hyperlink.
   * - Date
     - Date of creation of the attachment.
   * - User
     - User who created the attachment.


.. rubric:: Select an attachment

* Select an attachment depends on whether is a file or a hyperlink.

  * Click on |iconDownload| to download attachment file.
  * Click on |iconLink| to access to hyperlink.

.. rubric:: Document viewer

* Document viewer available for files: image, text, PDF and hyperlinks.
* Click on the icon.

.. rubric:: Delete an attachment

* Click on |buttonIconDelete| to delete an attachment.


.. raw:: latex

    \newpage

.. rubric:: Add an attachment

* Click on |buttonAdd| to add an attachment file to an item.

  * Dialog box "Attachment file" will be displayed.
 
* Click on |iconLink| to add hyperlink to an item.

  * Dialog box "Hyperlink" will be displayed. 

.. _attachment-file:

 .. compound:: **Attachment file**

     .. note:: To upload a file
 
        * Select file with "Browse" button or drop the file in "drop files here" area.
        * Attached files are stored on server side.
        * Attachments directory is defined in :ref:`Global parameters <file-directory-section>` screen.

    .. figure:: /images/GUI/BOX_attachmentFile.png
       :alt: Dialog box - Attachment for file
       :align: center

       Dialog box - Attachment for file
   

 .. compound:: **Hyperlink**

     .. note:: Hyperlink

        * Enter hyperlink in «Hyperlink» field.



    .. figure:: /images/GUI/BOX_attachmentHyperLink.png
       :alt: Dialog box - Attachment for hyperlink 	
       :align: center

       Dialog box - Attachment for hyperlink


.. tabularcolumns:: |l|l|

.. list-table:: Fields - Attachment dialog box
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Description
     - Description of attachment.
   * - Public
     - Attachment is visible to anyone.
   * - Team
     - Attachment is visible to every member of the creator’s team.
   * - Private
     - Attachment is visible only to the creator.


.. raw:: latex

    \newpage

.. index:: ! Note (section)

.. _note-section:

Notes section
-------------

This section allows to add notes on items of elements.

Notes are comments, that can be shared to track some information or progress.

.. rubric:: Predefined note

* The list of values appears whether a predefined note exists for an element or an element type.
* Selecting a predefined note  will automatically fill in the note text field.
* Predefined notes are defined in :ref:`predefined-notes` screen.

.. rubric:: Note visibility

* Public: Visible to anyone.
* Team: Visible to every member of the creator’s team.
* Private:  Visible only to the creator.

.. rubric:: Notes list

.. tabularcolumns:: |l|l|

.. list-table:: Fields of notes list
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the note.
   * - Note 
     - Text of the note.
   * - Date
     - Date of creation or modification of the note.
   * - User
     - Name of the user who created the note.

.. raw:: latex

    \newpage

.. rubric:: Notes list buttons

* Click on |buttonAdd| to add a note to an item. 
* Click on |buttonEdit| to edit the note.
* Click on |buttonIconDelete| to delete the note.

.. figure:: /images/GUI/BOX_Note.png
   :alt: Dialog box - Note 
   :align: center
