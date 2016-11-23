.. raw:: latex

    \newpage


.. contents:: Features
   :depth: 2
   :backlinks: top
   :local:

.. title:: Features

.. index:: ! Planning management

Planning management
-------------------

ProjeQtOr  provides all the elements needed to build a planning from workload, constraints between tasks and resources availability.

.. index:: Planning management (Project)

.. rubric:: Project

The project is the main element of ProjeQtOr.

It is also the highest level of visibility and definition of access rights based on profiles.

You can define profiles , some have visibility on all projects, others only on the projects they are assigned to.

You can also define sub-projects of a project and sub-project of sub-projects without limit to this hierarchical organization.

This allows for example to define projects that are not real projects , but just a definition of the structure for your organization.

.. index:: Planning management (Activity)
 
.. rubric:: Activity
 
An activity is a task that must be planned, or includes other activities.

This is usually a task that has a certain duration and should be assigned to one or more resources.

Activities appear on the Gantt Planning view.

.. index:: Planning management (Milestone)

.. rubric:: Milestone
 
A milestone is an event or a key date of the project.

Milestones are commonly used to track delivery dates or force a start date of activity.

They can also be used to highlight the transition from one phase to the next one.

Unlike activities , milestones have no duration and no work.

.. index:: Planning management (Resource)
.. rubric:: Resources
 
Resources can be assigned to activities.

This means that some work is defined on this activity for the resource.

Only the resources affected to the project of the activity can be assigned to the activity.

.. index:: Planning management (Real work allocation) 
.. rubric:: Real work allocation
 
Resources enter their time spent on the Real work allocation screen.

This allows for a real-time monitoring of work.

Moreover, updating the left work allows to recalculate the planning taking into account the actual progress on each task.

.. index:: Planning management (Planning)
.. rubric:: Planning
 
The planning is based on all the constraints defined:

* left work on each activity

* availability of resources

* rate of resource affectation to projects and assignment rate of resources to activities

* planning mode for each activity (as soon as possible, fixed duration, ... )

* dependencies between activities

* priorities of activities and projects

The planning is displayed as a Gantt chart.

.. index:: Planning management (Project portfolio)
.. rubric:: Project Portfolio
 
The planning can also be viewed as a Project Portfolio, which is a Gantt planning view restricted to one line per project, plus optionally selected milestones.

.. raw:: latex

    \newpage

.. index:: ! Resource management

Resource management
-------------------

ProjeQtOr  manages the availability of resources that can be affected to multiple projects. Tool calculates a reliable, optimized and realistic planning.

.. index:: Resource management (Resource)
.. rubric:: Resources

Resources are the persons working on the project activities.

A resource can also be a group of persons (team) for which you do not want to manage individual detail.

You can manage this through the capacity of the resource, that can be greater than 1 (for a group of people) or less than 1 (for a person working part-time).

.. index:: Resource management (Affectation)
.. rubric:: Affectations
 
The first step is to affect each resource to the projects on which it has to work, specifying the affectation rate (% of maximum weekly time spent on this project).

.. index:: Resource management (Assignment)
.. rubric:: Assignments
 
Then you can assign resources to project activities.

This means that some work is defined on this activity for the resource.

Only the resources affected to the project of the activity can be assigned to the activity.

.. index:: Resource management (Calendar)
.. rubric:: Calendars
 
To manage off days, you have a global calendar.

This calendar can be split into multiple calendars, to manage distinct availability types :

* you can create a calendar "80% " with every Wednesday as off day

* you can manage distinct holidays when working with international teams.

Each resource is then assigned to a calendar.

.. index:: Resource management (Real work allocation)
.. rubric:: Real work allocation
 
Resources enter their time spent on the Real work allocation screen. This allows for a real-time monitoring of work.

Moreover, updating the left work allows to recalculate the planning taking into account the actual progress on each task.


.. raw:: latex

    \newpage

.. index:: ! Tickets management

Tickets management
------------------ 

ProjeQtOr includes a Bug Tracker to monitor incidents on your projects, with possibility to include work on planned tasks of your projects.

.. index:: Tickets management (Ticket)
.. rubric:: Ticket

A Ticket is any intervention not needing to be planned (or that cannot be planned).
 
It is generally a short activity for which you want to follow advancement to describe (and possibly provide) a result.
 
For example, bugs or problems can be managed through Tickets:
 
* You can not schedule the bugs before they are identified and registered 
* You must be able to give a solution to a bug (workaround or fix).

.. index:: Tickets management (Ticket simple)

.. rubric:: Simple tickets

Simple tickets are just simplified representations of Tickets for users that will "create" tickets but not "treat" them.
 
Elements created as simple tickets are also visible as Tickets, and vice versa.


.. raw:: latex

    \newpage

.. index:: ! Costs management

Costs management
----------------

All elements related to delays can also be followed as costs (from resources work) and managing other expenses all costs of the project are monitored and can generate invoices.

.. index:: Costs management (Project)
.. rubric:: Projects

The Project is the main entity of ProjeQtOr.
In addition to tracking work on projects, ProjeQtOr can track the costs associated with this work.

.. index:: Costs management (Activity)
.. rubric:: Activities
 
An Activity is a task that must be planned, or includes other activities.
Work assigned to resources on activities is converted into associated costs.

.. index:: Costs management (Resource cost)
.. rubric:: Resource cost
 
To calculate the cost of expenses ProjeQtOr  defines the Resources cost.
This cost may vary depending on the role of the resource and may change over time.

.. index:: Costs management (Project expense)
.. rubric:: Project expenses
 
Projects expenses can also record expenses not related to resource costs (purchase , lease, sub-contracting).

.. index:: Costs management (Individual expense)
.. rubric:: Individual expenses
 
Individual expenses can record expenses generated by a given resource.

.. index:: Costs management (Quote)
.. index:: Costs management (Order)
.. index:: Costs management (Term)
.. index:: Costs management (Bill)

.. rubric:: Quote, Orders, Term, Bill
 
ProjeQtOr  can manage various financial elements found on a project: Quotation (proposals), Orders (received from customers), the invoicing Terms and Bills.


.. raw:: latex

    \newpage

.. index:: ! Quality management

Quality management
------------------

The specificity of ProjeQtOr  is that it is Quality Oriented : it integrates the best practices that can help you meet the quality requirements on your projects.

This way, the approval stage of your Quality Systems are eased, whatever the reference (ISO, CMMI, ...).

.. index:: Quality management (Workflow)
.. rubric:: Workflows

Workflows are defined to monitor changes of possible status.

This allows, among other things, to restrict certain profiles from changing some status.

You can, for instance, limit the change to a validation status to a given profile, to ensure that only an authorized user will perform this validation.

.. index:: Quality management (Ticket delay)
.. rubric:: Delays for tickets
 
You can define Delays for ticket. This will automatically calculate the due date of the Ticket when creating the Ticket.

.. index:: Quality management (Indicator)
.. rubric:: Indicators
 
Indicators can be calculated relative to respect of expected work, end date or cost values.

Some indicators are configured by default , and you can configure your own depending on your needs.

.. index:: Quality management (Alert)
.. rubric:: Alerts
 
Non respect of indicators (or the approach of non-respect target) can generate Alerts.

.. index:: Quality management (Checklist)
.. rubric:: Checklists
 
It is possible to define custom Checklists that will allow, for instance, to ensure that a process is applied.

.. index:: Quality management (Report)
.. rubric:: Reports
 
Many Reports are available to track activity on projects, some displayed as graphs.

.. rubric:: All is traced
 
Finally, thanks to ProjeQtOr , everything is traced.

You can follow-up, in a centralized and collaborative way, the various elements you used to follow-up (or not) in many Excel sheets : list of Questions & Answers, recording Decisions impacting the project, management of documents configuration, follow-up of meetings ...

In addition, all updates are tracked on each item to keep (and display) an history of the life of the item.

.. raw:: latex

    \newpage

.. index:: ! Risks management

Risks management
----------------

ProjeQtOr  includes a comprehensive risks and opportunities management, including the action plan necessary to mitigate or treat them and monitoring occurring problems.

.. index:: Risks management (Risk)
.. rubric:: Risks

A Risk is a threat or event that could have a negative impact on the project, which can be neutralized, or at least minimize, by predefined actions.

The risk management plan is a key point of the project management. Its objective is to :

* identify hazards and measure their impact on the project and their probability of occurrence,
* identify avoidance measures (contingency) and mitigation in case of occurrence (mitigation),
* identify opportunities,
* monitor the actions of risks contingency and mitigation,
* identify risks that eventually do happen (so they become issues).

.. index:: Risks management (Opportunity)
.. rubric:: Opportunities
 
An Opportunity can be seen as a positive risk. This is not a threat but an opportunity to have a positive impact on the project.

They must be identified and followed-up not to be missed out.

.. index:: Risks management (Issue)
.. rubric:: Issues
 
Issue is a risk that happens during the project.

If the risk management plan has been properly managed, the issue should be an identified and qualified risk.

.. index:: Risks management (Action)
.. rubric:: Actions
 
Actions shall be defined to avoid risks, not miss the opportunities and solve issues.

It is also appropriate to provide mitigation actions for identified risks that did not occur yet.


.. raw:: latex

    \newpage

.. index:: ! Perimeter management

Perimeter management
--------------------

ProjeQtOr allows you to monitor and record all events on your projects and helps you in managing of deviations, to control the perimeter of projects.

.. index:: Perimeter management (Meeting)
.. rubric:: Meetings

Follow-up and organize Meetings, track associated action plans, decisions and easily find this information afterwards.

.. index:: Perimeter management (Periodic meeting)
.. rubric:: Periodic meetings

You can also create Periodic meetings, which are regularly recurring meetings (steering committees, weekly progress meetings, ... )

.. index:: Perimeter management (Decision)
.. rubric:: Decisions
 
Decisions follow-up allows you to easily retrieve the information about the origin of a decision :

• who has taken a particular decision ?
• when?
• during which meeting ?
• who was present at this meeting?

Not revolutionary, this feature can save you many hours of research in case of dispute .

.. index:: Perimeter management (Question)
.. rubric:: Questions
 
Tracking Questions / Answers can also simplify your life on such exchanges, which often end up as a game of Ping - Pong with a poor Excel sheet in the role of the ball (when it is not a simple email exchange... ).

.. index:: Perimeter management (Product & Version)
.. rubric:: Product and Version

ProjeQtOr includes Product management and Product Versions.

Each version can be connected to one or more projects.

This allows you to link your activities to target version.

This also allows to know, in the case of Bug Tracking, the version on which a problem is identified and the version on which it is (or will be) fixed.




.. raw:: latex

    \newpage

.. index:: ! Document management

Documents management
--------------------
 
ProjeQtOr offers integrated **Document Management**.

This tool is simple and efficient to manage your project and product documents.

ProjeQtOr supported only digital document. Document file will be stored in the tool as versions.

Document can be versioning and an approver process can be defined.

.. rubric:: Directories structure management

* Allows to define a structure for document storage.
* Directories structure is defined in :ref:`document-directory` screen.

.. rubric:: Document management
  
* :ref:`document` screen allows to manage documents.

.. rubric:: Document access

* Global definition of directories is directly displayed in the document menu, to give direct access to documents depending on the defined structure.
* See: :ref:`menu-document-window`.

.. raw:: latex

    \newpage

.. index:: ! Commitments management

Commitments management
---------------------- 

ProjeQtOr  allows you to follow the requirements on your projects and measure at any time coverage progress, making it easy to reach your commitments.

In addition to the standard functionalities to manage your projects and monitor costs and delays, ProjeQtOr  provides elements to monitor commitments on products.

By linking these three elements, you can obtain a requirements covering matrix, simply, efficiently and in real time.

.. index:: Commitments management (Requirement)
.. rubric:: Requirements

Requirements management  helps in describing requirements explicitly and quantitatively monitor progress in building a product. 

.. index:: Commitments management (Test case)
.. rubric:: Test cases
 
The definition of Test cases is used to describe how you will test that a given requirement is met. 

.. index:: Commitments management (Test session)
.. rubric:: Test sessions
 
Test sessions group test cases to be executed for a particular purpose.


.. raw:: latex

    \newpage

.. index:: ! Tools
 
Tools
-----

ProjeQtOr includes some tools to generate alerts, automatically send emails on chosen events, import or export data in various formats.

.. index:: Tools (Import)
.. rubric:: Imports

ProjeQtOr includes an import feature for almost all elements of project management, from CSV or XLSX files.

.. index:: Tools (CSV & PDF export)
.. rubric:: CSV and PDF exports 
 
All lists of items can be printed and exported to CSV and PDF format.

The details of each item can be printed or exported in PDF format.

.. index:: Tools (MS-Project export)
.. rubric:: MS-Project export
 
The Gantt planning can be exported to MS-Project format (XML).

.. index:: Tools (Alert)
.. rubric:: Alerts
 
Internal alerts can be generated automatically based on defined events.

.. index:: Tools (Email)
.. rubric:: Emails
 
These alerts can also be dispatched as emails.

It is also possible to manually send emails from the application, attaching the details of an item.

It is also possible to retrieve answers to this type of email to save the message in the notes of the relevant item.

.. index:: Tools (Administration)
.. rubric:: Administration
 
ProjeQtOr provides administrative features to manage connections, send special alerts and manage background tasks treatments.

.. index:: Tools (CRON)
.. rubric:: CRON

Moreover, the tool features its own CRON system, independent of the operating system and able to handle the PHP stop and restart.
