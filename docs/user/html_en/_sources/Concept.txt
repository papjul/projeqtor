.. include:: ImageReplacement.txt

.. title:: Concepts


.. raw:: latex

    \newpage


Project
=======

A project is the main entity of ProjeQtOr.

Project element is more than a :ref:`planning-element`, it is also used to:

.. rubric:: Gather all project data

* Allows to gather all data depend on project:

  * Planning elements
  * Risk assessment, Risk mitigation, Reserve
  * Ticket, Issue, Bug tracking, Change request, Support
  * Review logs, Meeting, Decision, Action plan
  * Requirement & Test 
  * Project expense
  * Quotation, Order, Bill, Payment
  * Document

.. rubric:: Restrict data visibility

* Allows restricting data visibility to users by project.
* The project data visibility is granted according to the user profile.
* See: :ref:`profiles-definition`
* See: :ref:`project-affectation`


 .. compound:: **Project selector**

    * It is a filter that allows restricting the data visible to a dedicated project.
    * See: :ref:`top-bar`


.. raw:: latex

    \newpage

.. rubric:: Define the project type

Three project types can be defined:

 .. compound:: **Operational project**

    * Most common project to follow activity.

 .. compound:: **Administrative project**

    * Allows to follow the non productive work as holidays, sickness, training, …

    .. note::
       
       * All resource will be able to enter some real work on such project, without having to be affected to the project, nor assigned to project activities.

 .. compound:: **Template project**

    * Designed to define templates, to be copied as operational project. (See: :ref:`copy-item`)
    * Any project leaders can copy such projects, without having to be affected to them.

.. note:: 

   * The project type is defined in a project type (See: :ref:`project-type` screen).
   * Which is associated to a project (See: :ref:`project` screen).	

.. rubric:: Define billable project

A project can be billable or not.

 .. compound:: **Non billable project**

    * The non billable project is used for internal or administrative project.

 .. compound:: **Billable project**

    * For billable projects the billing types available are: at terms, on producing work, on capping produced work and manual.

.. note:: 

   * The project billing type is defined in a project type (See: :ref:`project-type` screen).
   * Which is associated to a project (See: :ref:`project` screen). 


.. raw:: latex

    \newpage

.. _product-concept:

Product
=======

A product is a material object or for IT/IS projects is a software application.

.. rubric:: Composition of product

* A product can have a complex structure that can be composed of sub-product and components.
* A product and its components can have several versions that represent each declination.
* See: :ref:`product-structure`

.. rubric:: Linked to a project

* A product is an element delivered by a project.
* The link with the project have no impact on project planning.
* Indicates only that project is devoted to a specific product versions.
* The link management is done in :ref:`project` and :ref:`product-version` screens.

.. figure:: /images/LinkProductToProject.png
   :alt: Link with projects
   :align: center

   Link with projects

.. rubric:: Identifying the version that is the subject of treatment

* Product (component) versions can be identified in these elements: :ref:`activity`, :ref:`milestone`, :ref:`requirement`, :ref:`test-case`, :ref:`test-session` and :ref:`ticket`.
* The purpose is to identify which product (component) and its version that is the subject of the treatment.
* Depending on the element, it has the possibility identifying version of  origin, target version or both.

.. rubric:: Document management

* Documents can be identified to products.
* See: :ref:`document`

.. rubric:: Management of product and component elements 

* See: :ref:`product-component-management`, for detail about management of product and component elements.

.. _product-structure:

Product structure
-----------------

The product structure is defined depending on the relationships defined between product and component elements.

The rules defining a product structure are:

 .. compound:: **Relationships between product elements**

    * A product can have several sub-products.
    * A sub-product can be in the composition only one product.

    .. figure:: /images/LinkProductSubProduct.png
       :alt: Relationships between product elements
       :align: center

       Relationships between product elements

 .. compound:: **Relationships between product and component elements**

    * A product can be composed of several components.
    * A component can be in the composition of several products.

    .. figure:: /images/LinkProductComponent.png
       :alt: Relationships between product and component elements
       :align: center

       Relationships between product and component elements

 .. compound:: **Relationships between component elements**

    * Components can be linked between them (N to N relationships).

    .. figure:: /images/LinkBetweenComponent.png
       :alt: Relationships between component elements
       :align: center

       Relationships between component elements

.. rubric:: Versions of product and component elements

* A product can have several versions that represent each declination of product.
* A component can have several versions that represent each declination of the component.
* Links can be defined between versions of products and components, but only with the elements defined in the product structure.


.. figure:: /images/LinkProductComponentVersion.png
   :alt: Link between versions of product and component
   :align: center

   Link between versions of product and component


.. raw:: latex

    \newpage


.. _planning:


Planning
========

ProjeQtOr implements work-driven planning method.

Based upon on resource availability and their capacity.

.. rubric:: Resource availability

* Resource availability is defined by calendars and project affectation period.

 .. compound:: **Resource calendar**

    * Each resource is attached to a calendar to define its working days.
    * Tasks assigned to the resource will be planned according to working days defined in the calendar.
    * More detail, see: :ref:`resource-calendar`

 .. compound:: **Project affectation period**

    * The resource can be affected to several projects.
    * Possibility to define affectation period.
    * More detail, see: :ref:`resource-affectation`

.. rubric:: Resource capacity

* Resource capacity is defined on daily base.
* The scheduling tool does not exceed the daily resource capacity.

.. topic:: Full Time Equivalent (FTE)
 
   * This indicator is defined for each resource.   
   * It allows to define the daily capacity.
   * More detail, see: :ref:`resource`

.. rubric:: Project affectation rate

* The project affectation rate is used to resolve affectation conflicts between projects.
* It allows to define resource availability for a project during a period.
* Use with the resource capacity, it allows to define the project affectation capacity on a weekly base.


.. rubric:: Task assignation rate

* The task assignation rate is used to keep some scheduling time for other tasks.
* Use with the resource capacity, it allows to define the assignation capacity on a daily base.

Draft planning
--------------

Two methods can be used to create a draft planning.

 .. compound:: **Use planning mode "fixed duration"**

    * This planning mode is used to define fixed duration tasks. (See: :ref:`planningMode`)
    * Dependencies allow to define the execution order of tasks. (See: :ref:`dependencies`)
    * You can define this planning mode as defaut in the Activities Types screen for some types of activities you'll use in draft plannings

 .. compound:: **Use faked and team resource**

    * The faked and team resource can be useful to get a first estimate of project cost and duration without involving the real resources.
    * Planning schedule is calculated using of the work-driven planning method.
    * Faked and team resources can be mixed in same draft planning.

     .. compound:: **Faked resources**

        * For instance, you want to define a Java developer resource. You can create a resource named "Java developer #1".
        * There are several levels of Java developer with different daily costs (beginner, intermediary and expert).
        * You can define for this resource the functions and average daily cost for each level. (See: :ref:`resource-function-cost`)
        * You assign this resource to tasks, to a specific function (level). (See: :ref:`assignment`)
        * Faked resource will be easily replaced with *real* resources when project becomes real, with *affectation replacement* feature. 

     .. compound:: **Team resource**

        * A team resource is a resource whose daily capacity has been defined to represent capacity of a team (Capacity (FTE) > 1).
        * For instance, you needed to define four Java developers, but you don’t want to create a resource for each. You can *overload* the daily capacity of the resource (Example: Capacity FTE=4).
        * Using team resources is very easy but renders estimation of project duration as draft, not taking into account constraint of different resources such as possibly different skills or expertise level.
        * With team resources it is very easy to estimate planning with different number of members in the team : what if I include 5 Java develpers instead of 4 ? Just change capacity to 5 and re-calculate planning...      


.. raw:: latex

    \newpage

.. _planning-element:

Planning elements
-----------------

ProjeQtOr offers standard planning elements like Project, Activity and Milestone.

But also, it offers two more planning element: Test session and Meeting.

.. rubric:: Project

This planning element defines the project.

* It allows to specify information on the project sheet like the customer, bill contact, sponsor, manager and objectives.
* Documents, notes and attachments can be annexed.
* More detail, see: :ref:`project` screen.


 .. compound:: **Sub-project**

    * Sub-project is used to split the project.
    * The project can be split to correspond the organizational breakdown or something else.

     .. admonition:: Separation of duties

        * A project can be split into multiple sub projects.
        * A project leader and team can be affected to each sub-project. 
        * Project affectation allows to define data visibility and isolate sub-projects. (See: :ref:`project-affectation`)
        * A supervisor can follow-up the project in its totality. 

        .. figure:: /images/SeparationDuties.png
           :alt: Separation of duties
           :align: center

           Separation of duties

.. raw:: latex

    \newpage


.. rubric:: Activity

This planning element can be a phase, a delivery, a task or any other activity.

An activity can grouped other activities or be a task.

 .. compound:: **Grouping of activities**

    * An activity can be the parent of activities.
    * This allows to define the structure of phases and deliveries.
    * Dates, works and costs of activities (child) are summarized in the activity (parent).

 .. compound:: **Task**

    * An activity is a task when it's not a parent of activities.
    * A task is assigned to resources for to be performed.

More detail, see: :ref:`activity` screen.


.. rubric:: Test session

This planning element is a specialized activity aimed for tests.

A test session allows to define a set of test case that must be run.

A test session can grouped other test sessions or be a task.

 .. compound:: **Grouping of test sessions**

    * A test session can be the parent of test sessions.
    * This allows to define the structure of test sessions.
    * Dates, works and costs of test sessions (child) are summarized in the test session (parent).

 .. compound:: **Task**

    * A test session is a task when it's not a parent of test sessions.
    * A task is assigned to resources for to be performed.

More detail, see: :ref:`test-session` screen.

.. raw:: latex

    \newpage

.. rubric:: Milestone

This planning element is a flag in the planning, to point out key dates.

May be a transition point between phases, deliveries.

ProjeQtOr offers two types of milestone floating and fixed.

More detail, see: :ref:`milestone` screen.

.. rubric:: Meeting

This planning element acts like a fixed milestone, but it's a task.

Like a milestone, a meeting can be a transition point. 

But also, like a task because it's possible to assign resources and planned work.

More detail, see: :ref:`meeting` screen.

.. raw:: latex

    \newpage

.. _dependencies:

Dependencies
------------

Dependencies allow to define the execution order of tasks (sequential or concurrent).

All planning elements can be linked to others.

Dependencies can be managed in the Gantt chart and in screen of planning element.

More detail, see: :ref:`project-planning`, :ref:`predSuces-element-section`.

.. note:: Global parameter "Apply strict mode for dependencies"

   * If the value is set to “Yes”, the planning element (successor) can't start the same day that the end date of planning element (predecessor). 

.. rubric:: Delay (days)

* A delay can be defined between predecessor and successor (start).


.. topic:: Dependency types

   * ProjeQtOr offers only the dependency (Finish to Start).
   * This section explains what are they dependency types can be reproduced or not.

    .. compound:: **Start to Start**

       * To reproduce this dependency type, it's possible to add a milestone as prior of both tasks.

    .. compound:: **Start to Finish** 

       * This dependency type can't be reproduced in ProjeQtOr.
       * This is a very rare scenario used.

    .. compound:: **Finish to Finish**

       * This dependency type can't be reproduced in ProjeQtOr.
       * This involves reverse planning and may introduce overloading of resources, what is not possible in ProjeQtOr.


.. raw:: latex

    \newpage

.. _planningMode:

Planning mode
-------------

Planning mode allows to define constraints on planning elements: activity, test session and milestone.

Planning modes are grouped under two types (Floating and Fixed).

.. rubric:: Floating

* These planning modes have no constraint date.
* Planning element is floating depending on its predecessors.
* Planning modes: As soon as possible, Work together, Fixed duration and floating milestone.


.. rubric:: Fixed

* These planning modes have constraint date.
* Planning modes: Must not start before validated date, As late as possible, Regular and fixed milestone.

More detail, see: :ref:`Activity and Test session  planning modes<progress-section-planning-mode>` and :ref:`Milestone planning modes<planning-mode-milestone>`.

.. note:: 

   * Because ProjeQtOr does not backward planning, the planning mode "As late as possible" with no constraint date  (Floating) is not available.

.. note:: Default planning mode

   * Possibility to define the default planning mode according to element type.
   * See: :ref:`activity-type`, :ref:`milestone-type` and :ref:`test-session-type` screens. 



.. raw:: latex

    \newpage


Prioritized planning elements
-----------------------------

Planning elements are scheduled in this order of priority:

#. Fixed date (Fixed milestone, Meeting)
#. Recurrent activities - Planning modes "Regular..." (Activity, Test session)
#. Fixed duration (Activity, Test session)
#. Others


.. _scheduling-priority:

Scheduling priority
-------------------

The scheduling priority allows to define scheduled order among planning elements.

Possible values: from 1 (highest priority) to 999 (lowest priority).

Scheduling priority value is set in progress section of planning element.

.. note::

   * If projects have different priorities, all elements of project with highest priority are scheduled first.


Project structure
-----------------

Work breakdown structure (WBS) is used to define project structure.

Breakdown can be done with sub-projects, activities and test sessions.

.. rubric:: Structure management

* As seen previously, the project can be split in subprojects.
* All other planning elements concerned by the project or subproject are put under them without structure.
* Planning elements can be grouped and orderly in hierarchical form.
* Structure management can be done in the Gantt chart or in planning elements screen.

.. rubric:: WBS element numbering

* The project is numbered by its id number.
* All other elements are numbered depending on their level and sequence.
* WBS numbering is automatically adjusted.

.. raw:: latex

    \newpage

Project scheduling calculation
------------------------------ 

The project schedule is calculated on the full project plan that includes parents and predecessor elements (dependencies).

.. rubric:: Scheduling

The calculation is executed task by task in the following order:

 #. Dependencies (Predecessor tasks are calculated first)
 #. Prioritized planning elements 
 #. Project priority
 #. Task priority
 #. Project structure (WBS)


.. rubric:: Constraints

The remaining work (left) on tasks will be distributed on the following days from starting planning date, taking into account several constraints:

* Resource availability
* Resource capacity
    
  * Project affectation capacity (Project affectation rate)
  * Assignation capacity (Task assignation rate)

* Planning mode


.. rubric:: Resource overloads

* This is not possible to overloading the resources. 
* The planning calculation process respects availability and capacity of the resource. 
* If it is not possible to distribute remaining work, on already planned days, the calculation process uses new available time slot.

.. raw:: latex

    \newpage

.. _projeqtor-roles:

ProjeQtOr roles
===============

A stakeholder can play many roles in ProjeQtOr.

Roles depends on :ref:`user-ress-contact-demystify`.

Specific roles are defined to allow:

* To categorize the stakeholders involved in the projects.
* To identify the stakeholders on items.
* To regroup the stakeholders to facilitate information broadcasting.


.. rubric:: Use to

* In items of elements.
* As reports parameters.
* As recipients list to mailing and alert.

--------------------------

.. glossary::

   Administrator

    * An administrator is a :term:`user` with "Administrator" profile.
    * Has a visibility over all the projects.

   Contact

    * A contact is a person in a business relationship.
    * A contact can be a person in the customer organization.
    * Used as contact person for contracts, sales and billing.
    * Contacts management is performed on :ref:`contact` screen.

   Issuer

    * An issuer is a :term:`user` who created the item.

    .. seealso:: Creation information

       * The issuer name and creation date of an item are displayed in the :ref:`Creation information zone<detail-window>`.

   Project leader

    * A project leader is a :term:`resource` affected to a project with a “Project Leader” profile.

   Project manager

    * A project manager is a :term:`resource` defined as the manager on a project.

    .. seealso:: Accelerator button

       * This button allows to set current user is the project manager.
       * More detail, see: :ref:`Assign to me button<assignToMe-button>`.  

   Project team

    * All :term:`resources<resource>` affected to a project.


   Requestor

    * A requestor is a :term:`contact`.
    * Used to specify the requestor for ticket, activity and requirement.
    * Only contacts affected to the selected project can be a requestor.  
 
   Responsible

    * A responsible is a :term:`resource` in charge of item treatment. 
    * Usually, the responsible is set when the status of the item is :term:`handled<Handled status>`.
    * Only resources affected to the selected project can be a responsible.  

    .. seealso:: GUI behavior

       * It is possible to define that responsible field is mandatory on handled status.
       * The element type screens allow to set this parameter to several elements. 
       * More detail, see: :ref:`behavior-section`. 

    .. seealso:: Set automatically the responsible

       * It is possible to set automatically the responsible.
       * More detail, see: :ref:`Global parameters<responsible-section>`. 	

    .. seealso:: Accelerator button

       * This button allows to set current user is the responsible.
       * More detail, see: :ref:`Assign to me button<assignToMe-button>`.

    .. seealso:: Access rights

       * It is possible to define a combination of rights to permit access for elements the user is responsible for.
       * More detail, see: :ref:`access-mode` screen.

   Resource

    * Human or material resource involved in the projects.
    * It is possible to define the resource availability to the projects.
    * Resources management is performed on the :ref:`resource` screen.


   User

    * User allows to connect to the application.
    * User profile define general access rights. But it does not necessarily give access to project data.
    * Users management is performed on the :ref:`user` screen.




.. raw:: latex

    \newpage


.. index:: ! Profile (Definition)

.. _profiles-definition:

Profiles definition
===================

The profile is a group used to define application authorization and access rights to the data.

A user linked to a profile belongs to this group who share same application behavior.

.. note::

   * You can define profiles to be conformed to the roles defined in your organization.
   * Access rights management is done on :ref:`Access rights<profile>` screens 


.. rubric:: Used for

* The profile is used to define access rights to application and data, first.
* Also, the profile is used to send message, email and alert to groups.

.. rubric:: Selected profile in project affectation

* A profile can be selected to a user, resource or contact in project affectation.
* The profile selected is used to give data access to elements of the projects.

.. rubric:: Workflow definition

* The profile is used to define who can change from one status to another one.
* You can restrict or allow the state transition to another one according to the profile.
* Workflow definition is managed in :ref:`workflow` screen.

.. raw:: latex

    \newpage

.. rubric:: Predefined profiles

* ProjeQtOr offer some predefined profiles.

 .. glossary::

    Administrator profile

     * This profile group all administrator users. 
     * Only these users can manage the application and see all data without restriction.
     * The user "admin" is already defined.

    Supervisor profile

     * Users linked to this profile have a visibility over all the projects.
     * This profile allows to monitor projects.

    Project leader profile

     * Users of this profile are the project leaders.
     * The project leader has a complete access to owns projects.
  
    Project member profile

     * A project member is working on projects affected to it.
     * The user linked to this profile is a  member of  team projects.

    Project guest profile

     * Users linked to this profile have limited visibility to projects affected to them.
     * The user "guest" is already defined.

.. rubric:: Predefined profiles (External)

* ProjeQtOr allow to involve client employees in their projects.
* The distinction between this profile and its equivalent, user access is more limited.



.. raw:: latex

    \newpage



.. _user-ress-contact-demystify:

Stakeholder definition
======================

ProjeQtOr allows to define roles of stakeholders.

The stakeholder definition is made with profile and a combination with user/resource/contact definition.

The combinations user/resource/contact allow to define:

* Connection to the application or not.
* Data visibility.
* Resource availability.
* Contact roles.

The next matrix shows the different possibilities.

.. list-table:: 
   :header-rows: 1
   :stub-columns: 1

   * - 
     - Connection
     - Visibility
     - Availability
   * - URC
     - |yes|
     - |yes|
     - |yes|
   * - UR
     - |yes|
     - |yes|
     - |yes|
   * - UC
     - |yes|
     - |yes|
     - |no|
   * - U
     - |yes|
     - |yes|
     - |no|
   * - R
     - |no|
     - |no|
     - |yes|
 


.. rubric:: Row legend

* U = User, R = Resource, C = Contact   

.. raw:: latex

    \newpage

.. rubric:: Data visibility

.. figure:: /images/Stakeholder-DataVisibility.png
   :alt: Stakeholder data visibility
   :align: center

   Data visibility diagram

|

 .. compound:: **User profile**

    * To a user, data visibility is based on its user profile.
    * User profile defined general access to application functionalities and data.
    * Base access rights defined if a user has access to own projects or over all projects.

 .. compound:: **All projects**

    * This access right is typically reserved for administrators and supervisors. 
    * Users have access to all elements of all projects.

 .. compound:: **Own projects**
    
    * Users with this access right must be affected to project to get data visibility.
    * Selected profile in affectation allows to define access rights on project elements.
    * For more detail, see: :ref:`project-affectation`.


.. raw:: latex

    \newpage


.. rubric:: Resource availability


.. figure:: /images/Stakeholder-ResourceAvailability.png
   :alt: Stakeholder resource availability
   :align: center

   Resource availability diagram

* Only resource can be assigned to project activities.
* Project affectation allows to define the resource availability on project.

 .. compound:: **Human resource**

    * Human resource is a project member.
    * Combined with a user, a human resource can connect to the application.

 .. compound:: **Material resource**

    * Material resources availability can be defined on projects.
    * But,  material resource must not  be  connected to the application.
    


.. rubric:: Contact roles  

 
* ProjeQtOr allows to involve contacts in projects.
* Combined with a user, a contact can connect to the application
* Combined with a resource, contact availability can be planned in projects.

.. figure:: /images/Stakeholder-ContactRoles.png
   :alt: Stakeholder contact roles
   :align: center

   Contact roles diagram


.. raw:: latex

    \newpage

Shared data
-----------

For a stakeholder, data on user, resource and contact are shared.

Project affection and user profile are also shared.

.. note::

   * For a stakeholder, you can define and redefine the combination without losing data.




.. raw:: latex

    \newpage


.. _project-affectation:

Project affectation
===================

Project affectation is used to:

* Defines project data visibility.
* Defines resource availability.
* Defines the period of access to project data by the user. 

.. note::

   * The :ref:`affectation` screen allows to manage overall project affectations. 

The following sections describe project affectation, performed for user, resource or contact.

User affectation
----------------

Project affectation gives data visibility on a project.

Project affectation can be defined in the :ref:`user` screen.

.. rubric:: Profile selection

* Selected profile allows to define access rights on project elements.

.. hint::

   * Selected profile allows to define the role played by the user in a project.
   * For instance, the user might be a project manager in a project and it could be a project member in another. 

   .. note:: 

      * Profile defined in project affectation does not grant or revoke access to users.
      * General access to application functionalities and data is defined by user profile. 

.. rubric:: Period selection

* Allow to define the period of project data visibility.

  .. hint::
 
     * Can be used to limit access period, according to services agreement.




.. raw:: latex

    \newpage

.. _resource-affectation:

Resource affectation
--------------------

Project affectation allows to define the resource availability on project.

A resource may be affected to projects at a specified rate for a period.

Project affectation can be defined in :ref:`project` and :ref:`resource` screens.

It is also possible to affect a team to a project in :ref:`team` screens.

.. note::

   * A resource affected to a project can be defined as :term:`responsible` of project items treatment.


.. rubric:: Period & Rate selection

* A resource may be affected to a project at a specified rate for a period. 

.. note::

   * If the period is not specified then the resource is affected throughout the project.

.. attention::

    * The planning calculator tries to plan, the remaining work on the task assigned to a resource within the project affection period.
    * If remaining work on the task can't be planned, a purple bar appears in the Gantt view.

.. rubric:: Change resource on a project affectation

* A resource can be changed on project affectation.
* All tasks assigned to old resource will be transferred to the new resource with planned work and remaining work.
* Work done on tasks belongs to always the old resource.


Multi-project affectation
^^^^^^^^^^^^^^^^^^^^^^^^^

A resource can be affected to multiple projects in the same period.

Make sure that the affectation to projects for a period not exceeding 100%.

In the section **Affectations** in :ref:`resource` screen, a tool allows to displayed conflicts.

.. hint:: How resolve conflicts?

   * You can change affectation period to avoid overlap between projects.
   * You can change the rate of affectation for it does not exceed 100% for the period.


Contact affectation
-------------------

A contact affected to a project can be defined as :term:`requestor`.

Project affectation can be defined in :ref:`project` and :ref:`contact` screens.



.. raw:: latex

    \newpage

.. _assignment:

Assignment
==========

The assignment is used to assign resources to project tasks (activity, test session, meeting).

Consists to assign a resource to a task in a specific function. The function allows to define the resource daily cost.

A resource assignment contains data about work on task (planned,  real, left and reassessed work).
    
.. note::

   * Only resources affected by the project can be assigned to project tasks.

.. note::

   * Assignment can be done in :ref:`activity`, :ref:`test-session` and :ref:`meeting` screens.



.. raw:: latex

    \newpage


.. index:: ! Resource (Function & Cost)

.. _resource-function-cost:

Resource function and cost
==========================

.. rubric:: Function

* The function defines the generic competency of a resource.
* It is used to define the role play by the resource on tasks.
* In real work allocation screen, the function name will be displayed in the real work entry.
* A main function must be defined to resource and it is used as default function.
* A daily cost can be defined for each function of the resource.
* The :ref:`function` screen allows to manage function list.

.. rubric:: Resource cost definition

* Allows to define the daily cost, according to the functions of the resource. 
* The daily cost is defined for a specific period.

.. rubric:: Real cost calculation

* When real work is entered, the real cost is calculated with work of the day and daily cost for this period. 

.. rubric:: Planned cost calculation

* When the project planning is calculated, resource cost is used to calculate planned cost.
* Planned cost is calculated with planned work of the day and current daily cost. 

.. note::
 
   * Function and cost are defined in :ref:`resource` screen.

.. raw:: latex

    \newpage

.. index:: ! Resource (Calendar) 

.. _resource-calendar:

Resource calendar
=================

A calendar defines the working days in a the year.

A calendar is defined for a type of resources and each resource is attached to a calendar.

.. rubric:: Planning process

* Calendars are used in the planning process which dispatches work on every working day. 
* During the planning process, the assigned work to a resource is planned in its working days.

 .. note:: 

    * You must re-calculate an existing planning to take into account changes on the calendar.

.. rubric:: Shows the availability of resources

* Working days defined in a calendar allows to show availability of resources.

---------------

.. rubric:: Default calendar

* The default calendar is used to define the working days in the year.
* By default, this calendar is defined for all resources.

.. rubric:: Specific calendar

* A specific calendar can be created to define working days for a type of resource.


------------------

.. note::

   * A calendar is set in :ref:`resource` screen. 
   * The calendar is defined in :ref:`calendar` screen.


------------------------

.. raw:: latex

    \newpage

.. rubric:: Use case

.. topic:: Public holiday

   * You can use the default calendar to set public holidays.

.. topic:: Work schedule

   * You can define a different work schedule to some resources.
   * This calendar defined exceptions to normal working days.
   * For instance, you can define a calendar for resources on leave on Wednesdays.

.. important:: Personal calendar

   * Even if you can create a specific calendar to each resource, this is not the advised way to manage personal days off and vacations.
   * You’d better use Administrative projects (enter real work in advance).


.. raw:: latex

    \newpage


.. _photo:

Photo
=====

A photo can be defined for a user, a resource and a contact.

It is a visual identification associated with the name.

.. note::

   * To enlarge, move the cursor over the picture.

.. rubric:: Photo management

* Click on |buttonAdd| or photo frame to add an image file. To complete instruction see: :ref:`Attachment file<attachment-file>`.
* Click on |buttonIconDelete| to remove  the image.
* Click on image to display the photo.

.. note::

   * Photo management can be done in :ref:`user-parameters`, :ref:`user`, :ref:`resource` , :ref:`contact` screens.

