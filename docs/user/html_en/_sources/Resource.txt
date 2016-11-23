.. include:: ImageReplacement.txt

.. raw:: latex

    \newpage


.. contents::
   :depth: 1
   :backlinks: top


.. title:: Resource

.. index:: ! Resource (Definition) 

.. _resource:

Resources
---------

.. sidebar:: Concepts 

   * :ref:`projeqtor-roles`
   * :ref:`profiles-definition`
   * :ref:`user-ress-contact-demystify`
   * :ref:`resource-function-cost`
   * :ref:`resource-calendar`
   * :ref:`photo`


Human and material resource are involved in the project.

Project affectation defines its availability.

.. rubric:: As group of person

* A resource can be a group of person.
* Useful in initial project planning.
* You create the fictitious resource with capacity > 1.

.. admonition:: Example

   * If you want a group of three peoples then resource capacity must be set to 3.

.. sidebar:: Other sections
   
   * :ref:`Affectations<affectations-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table:: 
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the resource.
   * - Photo
     - Photo of the resource.
   * - **Real name**
     - Name of the resource.
   * - User name
     - Name of user.
   * - Initials
     - Initials of the resource.
   * - Email address
     - Email address of the resource. 
   * - Profile
     - Profile of the user.
   * - Capacity (FTE)
     - Capacity of the resource, in Full Time Equivalent.
   * - **Calendar**
     - :ref:`Calendar<calendar>` defines the availability of the resource.
   * - :ref:`Team<team>`
     - The team to which the resource belongs.
   * - Phone
     - Phone number of the resource.
   * - Mobile
     - Mobile phone number of the resource.
   * - Fax
     - Fax number of the resource.
   * - Is a contact
     - Is this resource also a contact?
   * - Is a user
     - Is this resource also a user?
   * - :term:`Closed`
     - Flag to indicate that the resource is archived.
   * - Description
     - Complete description of the resource.

**\* Required field**

.. topic:: Field: Capacity (FTE)

   * 1 (full time).     
   * < 1 (for part time working resource).
   * > 1 (for virtual resource or teams, to use for instance to initialize a planning).

.. topic:: Field: Is a contact
   
   * Check this if the resource must also be a contact.
   * The resource will then also appear in the “Contacts” list. 

.. topic:: Field: Is a user

   * Check this if the resource must connect to the application.
   * You must then define the **User name** and **Profile** fields.
   * The resource will then also appear in the “Users” list. 



.. raw:: latex

    \newpage

.. index:: ! Resource (Function & Cost definition)

.. rubric:: Section: Function and cost

This section allows to define functions and cost of the resource.

 .. compound:: **Main function**

    * Main function allows to define the default function.

 .. compound:: **Resource cost definition**

    * Allows to define the daily cost, according to the functions of the resource. 
    * The daily cost is defined for a specific period.

.. list-table:: Function and cost section fields
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Function
     - Function of the resource for the selected cost.
   * - Cost
     - Daily cost of the resource for the selected function.
   * - Start date
     - Start date for the cost of the resource, for the selected function.
   * - End date
     - End date for the cost of the resource, for the selected function.

.. topic:: Field: End date

   * The end date is set when a new resource cost is defined in the same function.
   * The end date is the day before the start date in the new resource cost entry. 


.. raw:: latex

    \newpage

.. rubric:: Resource cost management

* Click on |buttonAdd| to create a new resource cost. 
* Click on |buttonEdit| to update an existing resource cost.
* Click on |buttonIconDelete| to delete the resource cost.

.. figure:: /images/GUI/BOX_ResourceCost.png
   :alt: Resource cost dialog box
   :align: center

   Resource cost dialog box

.. list-table:: Resource cost dialog box fields
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Function
     - Function to be selected.
   * - Cost
     - Daily cost of the resource for the selected function.
   * - Start date
     - Start date for the cost of the resource, for the selected function.

.. topic:: Field: Function

   * The default value will be the main function. 

.. topic:: Field: Start date

   * Start date must be set when a new resource cost is created for the same function.


.. rubric:: Section: Miscellanous

.. list-table:: Miscellanous section fields
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Don't receive team mails
     - Box checked indicating that the resource doesn't want to receive mails sent to the team.
 







.. raw:: latex

    \newpage

.. index:: ! Team
.. index:: ! Resource (Team) 

.. _team:

Teams
-----

The team is a group of resources gathered on any criteria.

.. note::

   * A resource can belong to only one team.


.. rubric:: Use for

* To affect all team members of a project.
* To filter resource data in work, cost and planning reports.
* To set attachment, note and document visibility to the team.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table:: Description section fields
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the team.
   * - **Name**
     - Name of the team.
   * - :term:`Closed`
     - Flag to indicate that team is archived.
   * - Description
     - Complete description of the team.
 
.. rubric:: Section: Team members

* List of the resources members of the team.

.. topic:: Button: Affect all team members of a project

   * This button allows to affect all team members of a project.
   * The :ref:`affectation dialog box<affectations-box>` will be displayed.

.. raw:: latex

    \newpage

.. index:: ! Calendar definition
.. index:: ! Resource (Calendar definition)  

.. _calendar:

Calendar
--------

.. sidebar:: Concepts 

   * :ref:`resource-calendar`

This tool allows to define calendars.

.. rubric:: How it works

* It allows to define exceptions to the default definition.
* In default definition, week days are days work and weekend days are days off.

.. note::

   * Exceptions can be defined for the current year and the next years.



.. rubric:: Default calendar

* A calendar named "default" is already defined.
* By default, this is the calendar of all resources.
* Days off defined in this calendar is displayed in Gantt, real work allocation and diary.
* It cannot be deleted.

.. rubric:: Specific calendar

* A specific calendar can be created to define days off and work to a resource.
* Days off defined in this calendar is displayed in real work allocation and diary.

.. rubric:: Import calendar definition

* It is possible to import exceptions definition of a calendar in another.
* Existing exceptions of current calendar are not changed.

.. note::

   * The calendars are not linked.
   * You have to re import the definition to apply changes. 

.. raw:: latex

    \newpage


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table:: Description section fields
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the calendar.
   * - Name
     - Name of the calendar.
 
.. rubric:: Section: Year

.. topic:: Field : Year field

   * Select year of displayed calendar.

.. topic:: Button : Import this year from calendar 

   * Copy exceptions of the selected year of the selected calendar into current calendar.

.. rubric:: Section: Calendar days

.. figure:: /images/GUI/SEC_YearCalendar.png
   :alt: Year calendar
   :align: center

   Year calendar


A calendar of selected year is displayed to give a global overview of the exceptions existing.

* In white, days work.
* In gray, days off.
* In red, exception days work. 
* In blue, exception days off. 
* In bold, current day. 

Just click on one day in the calendar to switch between off and work day.

