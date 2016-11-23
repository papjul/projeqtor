.. include:: ImageReplacement.txt


.. title:: Planning elements


.. raw:: latex

    \newpage

Planning elements
=================


.. contents:: Planning elements
   :local: 
   :backlinks: top

The planning elements :ref:`test-session` and :ref:`meeting` are described in their topics.


.. index:: ! Project 

.. _project:

Projects
--------

The project is the primary entity of ProjeQtOr.

It is also the top-level of visibility, depending on profiles.




.. rubric:: Sub-projects 

* You can also define sub-projects on a project.
* Then, the project may not be real projects, but just organizational breakdown corresponding to the organization.


.. rubric:: Fix planning 

* When box “fix planning” is checked, the project scheduling won’t be remade.
* This will avoid change on planned values.

.. rubric:: Under construction

* When a project is under construction, no automatic email is sent for the elements of the project.
* A project can be under construction as long as it isn’t stated (set to *handled* status).


.. rubric:: Manual indicators

* Fields: Health status, Quality level, Trend and Overall progress are manual indicators.
* They allow define visual way the general health, conformity, trend and progress of the project.
* Some manual indicators are displayed on the Today screen.  

.. rubric:: Special fields

* The value of the fields the Project name, Sponsor, Project code and Contract code can be used as the substitute value of special fields.
* See: :ref:`administration-special-field-label`

.. rubric:: Monitoring indicator

* The indicators can be defined on the progress data.
* See: :ref:`progress-section-monitor-indicator`

.. _project-reserve:

.. index:: ! Project (Reserve)

.. rubric:: Project reserve

* Project reserve is an amount reserved based on identifying risks and opportunities.
* Calculated from contingency reserve of risks minus potential gain of opportunities.
* ProjeQtOr uses a technique as "Expected monetary value (EMV)" to convert risks and opportunities into an amount.
* The amount of project reserve will be added to remaining amount.
* See: :ref:`risk` and :ref:`opportunity`


.. sidebar:: Other sections

   * :ref:`Progress<progress-section-activity>`
   * :ref:`Affectations<affectations-section>`
   * :ref:`Versions linked to this project<Link-version-project-section>`  
   * :ref:`Types restrictions<type-restriction-section>`  
   * :ref:`Predecessor and Sucessor element<predSuces-element-section>`
   * :ref:`Linked element<linkElement-section>`   
   * :ref:`Attachments<attachment-section>`   
   * :ref:`Notes<note-section>`   

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the project.
   * - **Name**
     - Short name of the project.
   * - **Type**
     - Type of project.
   * - Customer
     - The customer of the project.
   * - Bill contact
     - Billing contact.
   * - Project code
     - Code of the project.
   * - Contract code
     - Code of the contract of the project.
   * - Customer code
     - Code of the customer of the project.
   * - Is sub-project of
     - Name of the top project if this project is a sub-project. 
   * - Sponsor
     - Name of the sponsor of the project.
   * - :term:`Manager<Project manager>`
     - Name of the resource who manages the project.
   * - Color
     - Color of the project, to be displayed in some reports.
   * - **Status**
     - Actual :term:`status` of the project.
   * - Health status
     - Global health status of the project.
   * - Quality level
     - Estimation of quality level of project (result of audits).
   * - Trend
     - Trend of global project health.
   * - Overall progress
     - Overall progress to be selected in a defined list.
   * - Fix planning
     - Box checked indicates the planning of the project is frozen, and its sub-projects.
   * - Under construction
     - Box checked indicates the project is under construction.
   * - :term:`Done`
     - Box checked indicates the project is finished.
   * - :term:`Closed`
     - Box checked indicates the project is archived.
   * - Cancelled
     - Box checked indicates the project is cancelled.
   * - :term:`Description`
     - Complete description of the project.
   * - Objectives
     - Objectives of the project.

**\* Required field**

.. topic:: Field: Customer

   * The value of the field is used in the Quotations, Orders and Bills concerned with the project.

.. topic:: Field: Bill contact

   * The value of the field is used in Bills concerned with the project.



.. rubric:: Section: Sub projects

* List of sub-projects under for the project.


.. raw:: latex

    \newpage

.. index:: ! Activity 

.. _activity:

Activities
----------

An activity is a kind of task that must be planned, or that regroups other activities.

An activity can be:

* Planned tasks.
* Change requests.
* Phases.
* Deliveries.
* Versions or releases.

.. rubric:: Assigned resources

* Resources are assigned to activities.
* During an assignation, some work is initially planned on this activity for the resource.

.. rubric:: Real work allocation

* For a resource, assigned activities are tasks in which he can entry the real work.

.. rubric:: Activities regroupment

* Activities can have parents to regroup activities. 
* So a WBS (work breakdown structure number) is calculated for the activities.
* Activities can be sorted inside their parent activity, on the Gantt planning view, using drag and drop.
* Parent activity must belong to the same project.

.. rubric:: **Work on tickets**

* Tickets can be linked to an activity (task).
* Work on tickets will be included in the activity.
* More detail, see: :ref:`ticket` screen.

 .. compound:: **Assignment of resources to task**

    * Resources who will work on the ticket doesn't need to be assigned to linked activity before.
    * The assignment will be automatically created once that resource has entered the working time (real work) in the ticket.
    * The assigned task and the sum of working time entries by resource in tickets will be available in its timesheet.


.. rubric:: Monitoring indicator

* The indicators can be defined on the progress data.
* See: :ref:`progress-section-monitor-indicator`

.. sidebar:: Other sections

   * :ref:`Assignment<assignment-section>`
   * :ref:`Progress<progress-section-activity>`
   * :ref:`Predecessor and Sucessor element<predSuces-element-section>`
   * :ref:`Linked element<linkElement-section>`   
   * :ref:`Attachments<attachment-section>`   
   * :ref:`Notes<note-section>`   

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table:: 
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the activity.
   * - **Name**
     - Short description of the activity.
   * - **Activity type**
     - Type of activity.
   * - **Project**
     - The project concerned by the activity.
   * - :term:`External reference`
     - External reference of the activity.
   * - :term:`Requestor`
     - Contact at the origin of the activity.
   * - :term:`Origin`
     - Element which is the origin of the activity.
   * - :term:`Description`
     - Complete description of the activity.

**\* Required field**

.. rubric:: Section: Treatment

.. tabularcolumns:: |l|l|

.. list-table:: 
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Parent activity
     - Parent activity for grouping purpose. 
   * - **Status**
     - Actual :term:`status` of the activity.
   * - :term:`Responsible`
     - Resource who is responsible for the activity.
   * - :term:`Handled`
     - Box checked indicates the activity is taken over.
   * - :term:`Done`
     - Box checked indicates the activity has been treated.
   * - :term:`Closed`
     - Box checked indicates the activity is archived.
   * - Cancelled
     - Box checked indicates the activity is cancelled.
   * - Target version
     - The target version of the product affected in delivery of the activity.	
   * - :term:`Result`
     - Complete description of the treatment done on the activity. 
 
**\* Required field**

.. topic:: Field: Target version

   * Contains the list of product versions linked to the project.
   * More detail, see: :ref:`Product concept<product-concept>`

.. raw:: latex

    \newpage

.. index:: ! Milestone

.. _milestone:

Milestones
----------

A milestone is a flag in the planning, to point out key dates.

Milestones are commonly used to check delivery dates.

They can also be used to highlight the transition from one phase to the next.

ProjeQtOr offers two types of milestone:

 .. compound:: **Floating milestone**

    * This milestone will automatically move to take into account dependencies.

 .. compound:: **Fixed milestone**

    * This milestone is fixed in the planning, not taking into account predecessor dependencies.
    * This kind of milestone is interesting, for instance to set-up start date for some tasks.
    * Fixed date is set by validated date.

.. rubric:: Monitoring indicator

* The indicators can be defined on the progress data.
* See: :ref:`progress-section-monitor-indicator`

.. sidebar:: Other sections

   * :ref:`Progress<progress-section-milestone>`
   * :ref:`Predecessor and Sucessor element<predSuces-element-section>`
   * :ref:`Linked element<linkElement-section>`   
   * :ref:`Attachments<attachment-section>`   
   * :ref:`Notes<note-section>`   

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table:: 
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the milestone.
   * - **Name**
     - Short description of the milestone.
   * - **Milestone type**
     - Type of milestone.
   * - **Project**
     - The project concerned by the milestone.
   * - :term:`Origin`
     - Element which is the origin of the milestone.
   * - :term:`Description`
     - Long description of the milestone.

**\* Required field**


.. rubric:: Section: Treatment

.. tabularcolumns:: |l|l|

.. list-table:: 
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Parent activity
     - Parent activity for grouping purpose. 
   * - **Status**
     - Actual :term:`status` of the milestone.
   * - :term:`Responsible`
     - Resource who is responsible for the milestone.
   * - :term:`Handled`
     - Box checked indicates the milestone is taken over.
   * - :term:`Done`
     - Box checked indicates the milestone has been treated.
   * - :term:`Closed`
     - Box checked indicates the milestone is archived.
   * - Cancelled
     - Box checked indicates the milestone is cancelled.
   * - Target version
     - The target version of the product affected in delivery of the milestone.	
   * - :term:`Result`
     - Complete description of the treatment done on the milestone. 
 
**\* Required field**

.. topic:: Field: Target version

   * Contains the list of product versions linked to the project.
   * More detail, see: :ref:`Product concept<product-concept>`

