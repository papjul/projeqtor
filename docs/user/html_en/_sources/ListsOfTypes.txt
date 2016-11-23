.. raw:: latex

    \newpage

.. title:: Lists of types

.. raw:: latex

    \newpage

.. _type-restriction-section:

Types restrictions
------------------

Allows to limit values displayed in the list of values for each element type.

Restrictions can be defined for a project, a project type or a profile.

.. note:: Types restrictions section on Project screen.

   * To display types restrictions section, the global parameter "allow type restriction on project" must be set to "Yes".
   * Possibility to define more restrictions to a project against restrictions defined at the project type level.


.. rubric:: Type restriction management

#. Click on "Restrict types" button to display the dialog box.
#. For each element type, select values that will be in the list of values.

.. figure:: /images/GUI/BOX_RestrictTypeForProject.png
   :alt: Dialog box - Restrict types for the project 
   :align: center

.. rubric:: Displays element type names where a restriction is applied

.. figure:: /images/GUI/ZONE_TypeRestrictionSection.png
   :alt: Section - Restrict types 
   :align: center


.. raw:: latex

    \newpage

.. index:: ! Project (Type)

.. _project-type:

Projects types
--------------

Project type is a way to define common behavior on group of projects.


.. glossary::

   Code of the project type

      * Some important behavior will depend on code of the project type.

      **OPE** : Operational project

          * Most common project to follow activity.

          .. note::

             All new types are created with **OPE** code.

      **ADM** : Administrative project

          * Type of project to follow non productive work : holidays, sickness, training, …
          * Every resource will be able to enter some real work on such projects, without having to be affected to the project, nor assigned to project activities.
          * Assignments to all project task will be automatically created for users to enter real work.

      **TMP** : Template project 

          * These projects will not be used to follow some work.
          * They are just designed to define templates, to be copied as operational projects.
          * Any project leader can copy such projects, without having to be affected to them.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`
   * :ref:`Types restrictions<type-restriction-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - :term:`Code of the project type`.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - Billing type
     - Will define billing behavior (see: :term:`Billing types`).
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**



.. raw:: latex

    \newpage


.. index:: ! Ticket (Type)

.. _ticket-type:

Tickets types
-------------

Ticket type is a way to define common behavior on group of tickets.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**



.. index:: ! Activity (Type)

.. _activity-type:

Activities types
----------------

Activity type is a way to define common behavior on group of activities.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - **Default planning mode**
     - Default planning mode for type. 
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**

.. raw:: latex

    \newpage


.. index:: ! Milestone (Type)

.. _milestone-type:

Milestones types
----------------

Milestone type is a way to define common behavior on group of milestones.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - **Default planning mode**
     - Default planning mode for type. 
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**

  


.. index:: ! Quotation (Type)

.. _quotation-type:

Quotations types
----------------

Quotation type is a way to define the way the concerned activity should be billed.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**


.. raw:: latex

    \newpage

.. index:: ! Order (Type)

.. _order-type:

Orders types
------------

Order type is a way to define the way the activity references by the order will be billed.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**




.. index:: ! Expense (Individual expense type)

.. _individual-expense-type:

Individual expenses types
-------------------------

Individual expense type is a way to define common behavior on group of individual expense.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**

.. raw:: latex

    \newpage

.. index:: ! Expense (Project expense type)

.. _project-expense-type:

Project expenses types
----------------------

Project expense type is a way to define common behavior on group of project expense.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**

.. raw:: latex

    \newpage

.. index:: ! Expense (Detail type)

.. _expense-detail-type:

Expenses details types
----------------------

Expense detail type is a way to define common behavior and calculation mode on group of expense details.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Sort order
     - Number to define order of display in lists.
   * - Value / unit
     - Define calculation mode for the detail type. 
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**

.. topic:: Field: Value / unit
   
    * If unit is set and not value, this line will be imputable.
    * If both unit and value are set, the line will be read only.
    * Result cost will be the multiplication between each of the three non empty line values.

.. rubric:: Section: Scope

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Individual expense
     - Details type of individual expense.
   * - Project expense
     - Details type of project expense.

.. raw:: latex

    \newpage

.. index:: ! Bill (Type)

.. _bill-type:

Bills types
-----------

Bill type is a way to define common behavior on group of bills.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**

.. index:: ! Payment (Type)

.. _payment-type:

Payments types
--------------

Payment type is a way to define common behavior on group of payments.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**


.. raw:: latex

    \newpage


.. index:: ! Risk (Type)

.. _risk-type:

Risks types
-----------

Risk type is a way to define common behavior on group of risks.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**




.. index:: ! Opportunity (Type)

.. _opportunity-type:

Opportunities types
-------------------

Opportunity type is a way to define common behavior on group of opportunities.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**


.. raw:: latex

    \newpage


.. index:: ! Action (Type)

.. _action-type:

Actions types
-------------

Action type is a way to define common behavior on group of actions.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**



.. index:: ! Issue (Type)

.. _issue-type:

Issues types
------------

Issue type is a way to define common behavior on group of issues.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**

.. raw:: latex

    \newpage

.. index:: ! Meeting (Type)

.. _meeting-type:

Meetings types
--------------

Meeting type is a way to define common behavior on group of meetings.

.. note::
   * Meeting type is also used for periodic meetings definition.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**




.. index:: ! Decision (Type)

.. _decision-type:

Decisions types
---------------

Decision type is a way to define common behavior on group of decisions.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**


.. raw:: latex

    \newpage


.. index:: ! Question (Type)

.. _question-type:

Questions types
---------------

Question type is a way to define common behavior on group of questions.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**



.. index:: ! Message (Type)

.. _message-type:

Messages types
--------------

Message type is a way to define common behavior on group of messages (appearing on today screen).

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Color
     - Display color for messages of this type.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**


.. raw:: latex

    \newpage

.. index:: ! Document (Type)

.. _document-type:

Documents types
---------------

Document type is a way to define common behavior on group of documents.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**



.. index:: ! Context (Type)

.. _context-type:

Contexts types
--------------

Context types are used to define the environmental context to describe ticket or test case.

Only three context types exist, corresponding to the three selectable fields. (Environment, OS and Browser)

.. note::

   * Only the name of the context types can be changed.
   * No new context type can be added.
   * No context type can be deleted.

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Description
     - Description of the type.

..   * - :term:`Closed`
..     - Box checked indicates the type is archived.


**\* Required field**

.. raw:: latex

    \newpage

.. index:: ! Requirement (Type)

.. _requirement-type:

Requirements types
------------------

Requirement type is a way to define common behavior on group of requirements.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**



.. index:: ! Test case (Type)

.. _test-case-type:

Test cases types
----------------

Test case type is a way to define common behavior on group of test cases.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**

.. raw:: latex

    \newpage


.. index:: ! Test session (Type)

.. _test-session-type:

Test sessions types
-------------------

Test session type is a way to define common behavior on group of test sessions.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - **Workflow**
     - Defined the workflow ruling status change for items of this type (see: :ref:`workflow`).
   * - **Default planning mode**
     - Default planning mode for type. 
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**



.. index:: ! Customer (Type)

.. _customer-type:

Customers types
---------------

Customer type is a way to define different kinds of customers  (prospects or clients).

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**


.. index:: ! Provider (Type)

.. _provider-type:

Providers types
---------------

Provider type is a way to define different kinds of providers.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**



.. raw:: latex

    \newpage


.. index:: ! Product (Type)

.. _product-type:

Products types
--------------

Product type is a way to define common behavior to group of product.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**



.. index:: ! Component (Type)

.. _component-type:

Components types
----------------

Component type is a way to define common behavior to group of component.

.. sidebar:: Other sections

   * :ref:`Behavior <behavior-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the type.
   * - **Name**
     - Name of the type.
   * - Code
     - Code of the type.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Box checked indicates the type is archived.
   * - Description
     - Description of the type.

**\* Required field**


.. raw:: latex

    \newpage


.. _behavior-section:

Behavior section
----------------

This section is common to several element types.

Allows to determine some GUI behavior, according to element types.

.. note::
 
   * Depending on the element type the following fields can be displayed.


.. rubric:: Description or Comments

* Box checked indicates the field “:term:`Description`” is mandatory.

.. rubric:: Responsible

* Box checked indicates the field ":term:`Responsible`" is mandatory when the status to treatment of the item is "handled".  

.. rubric:: Result

* Box checked indicates the field ":term:`Result`" is mandatory when the status to treatment of the item is "done". 
 
.. rubric:: Flag status

* Fields: Lock handled, Lock done, Lock closed and Lock cancelled
* Those fields allow to determine whether the checkbox fields concerned are locked or not.
* When a flag status is locked, move to this status through status change.

Ticket type
^^^^^^^^^^^

.. rubric:: Resolution

* Box checked indicates the field "Resolution" is mandatory when the status to treatment of an item is "done".  

.. rubric:: Lock solved

* Box checked indicates the field “Solved” is read only.
* The value of field must come from the field "Solved" defined in the selected resolution.





