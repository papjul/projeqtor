.. include:: ImageReplacement.txt

.. title:: Controls & Automation

.. index:: ! Workflow

.. _workflow:

Workflows
---------

.. sidebar:: Concepts 

   * :ref:`profiles-definition`

A workflow defines the possibility to go from one status to another one, and who (depending on profile) can do this operation for each status.

Once defined, a workflow can be linked to any type of any item. 

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the workflow.
   * - **Name**
     - Name of the workflow.
   * - Sort order
     - Number to define the order of display in lists.
   * - :term:`Closed`
     - Flag to indicate that workflow is archived.
   * - Description
     - Complete description of the workflow.

**\* Required field**

.. raw:: latex

    \newpage

.. rubric:: Button: Select status to show or hide

* This button |buttonIconParameter|  can be used to hide some unnecessary status.

.. figure:: /images/GUI/BOX_SelectStatusToShowOrHide.png
   :alt: Dialog box - Select status to show or hide 
   :align: center


.. rubric:: Section: Workflow Diagram

* The workflow diagram presents a visual representation of the workflow displaying all possible transitions (independently to profile rights).

.. figure:: /images/GUI/SEC_WorkflowDiagram.png
   :alt: Workflow Diagram
   :align: center

   Workflow Diagram


.. raw:: latex

    \newpage

.. rubric:: Section: Habilitation to change from a status to another

* The habilitation table helps defining who can move from one status to another one.
* Each line corresponds to the status from which you want to be able to move.
* Each column corresponds to the status to which you want to be able to go.
* It is not possible to go from one status to itself (these cells are blank).
* Just check the profile (or “all”) who is allowed to pass from one status to the other.


 .. figure:: /images/GUI/SEC_HabilitationTable.png
    :alt: Habilitation table
    :align: center

    Habilitation table


 .. compound:: **In the upper example:**

    * Anyone can move an item from “recorded” to “assigned” and from “recorded” to “cancelled”.
    * No one can move an item from “qualified” status to any other status. In this case, pay attention that it must never be possible to move an item to “qualified” status, because it will not be possible to leave this status.

.. raw:: latex

    \newpage

.. index:: ! Email (Event)

.. _mail-on-event:

Mails on event
--------------

The application is able to automatically send mails on updating event.

Events are defined on an element and element type.


.. note::

   * The mail message is formatted to display item information.
   * Mail titles is defined in :ref:`Global parameters<mail-titles>` screen.

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the event.
   * - Element updated
     - Type of elements that will be concerned by automatic emailing.
   * - Type
     - Type of the selected element updated. 
   * - New status
     - Positioning the elements to this status will generate an email.
   * - Or other event
     - Other event that will possibly generate an email.
   * - :term:`Closed`
     - Flag to indicate that status mail is archived.


.. topic:: Field: Type

   * If not set, the event is valid for every type of the element.


.. rubric:: Section: Mail receivers

* List of addresses of the mails.
* The list is not nominative, but defined as roles on the element.
* Each addressee will receive mail only once, even if a person has several “checked” roles on the element.
* See: :ref:`receivers-list` for receivers detail.

.. raw:: latex

    \newpage

.. index:: ! Ticket (Delay)

.. _delay-for-ticket:

Delays for tickets
------------------

It is possible to define a default delay for tickets, for each ticket type and each ticket urgency.

.. note::

   * On creation, the due date will automatically be calculated as creation date + delay.

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the delay definition.
   * - **Ticket type**
     - Ticket type the delay applies to.
   * - **Urgency**
     - Urgency of ticket the delay applied to.
   * - **Value**
     - Value of delay.
   * - :term:`Closed`
     - Flag to indicate that delay definition is archived.

**\* Required field**

.. topic:: Field: Value

   * Unit for the value can be :
    
     - Days : simple calculation as days.
     - Hours : simple calculation as hours.
     - Open days : calculation excluding days off (weekends and days off defined on “calendar”).
     - Open hours : calculation only on the “standard open hours” defined in :ref:`Global parameters<daily-work-hours-section>` screen. 




.. raw:: latex

    \newpage

.. index:: ! Indicator (Definition)
.. index:: ! Email (Indicator)
.. index:: ! Internal alert (Indicator)

.. _indicator:

Indicators
----------

It is possible to define indicators on each type of element.

Depending on type of elements the type of indicators that can be selected in list differs.

Some indicators are based on delay (due date), some on work, some on cost.

For each indicator a warning value and an alert value can be defined.

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the indicator definition.
   * - Element
     - The elements the indicator applies to.
   * - Type
     - Type of the elements the indicator applies to.
   * - Indicator
     - Indicator applies to.
   * - Reminder
     - Delay before due date or % of work or % or cost to send a warning.
   * - Alert
     - Delay before due date or % of work or % or cost to send an alert.
   * - :term:`Closed`
     - Flag to indicate that delay definition is archived.

.. rubric:: Section: Mail receivers

* List of addresses of the mails.
* The list is not nominative, but defined as roles on the element.
* Each addressee will receive mail only once, even if a person has several “checked” roles on the element. 
* See : :ref:`receivers-list` for receivers detail.

.. rubric:: Section: Internal alert receivers

* List of addresses of the internal alert.
* The list is not nominative, but defined as roles on the element.
* See : :ref:`receivers-list` for receivers detail.

.. raw:: latex

    \newpage

.. index:: ! Predefined notes
.. index:: ! Note (Predefined)

.. _predefined-notes:

Predefined notes
----------------

The predefined note set the possibility to define some predefined texts for notes.

When some predefined notes are defined for an element and / or type a list will appear on note creation.

Selecting an item in the list will automatically fill in the note text field.

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the predefined note.
   * - **Name**
     - Name of the predefined note.
   * - Element
     - Kind of item (Ticket, Activity, …) for which this predefined note will be proposed on note creation.
   * - Type
     - Type of element for which this predefined note will be proposed on note creation.
   * - :term:`Closed`
     - Flag to indicate that delay definition is archived.
   * - Text
     - Predefined text for notes.

**\* Required field**

.. topic:: Field: Element

   * If not set, predefined note is valid for every element type.

.. topic:: Field: Type

   * If not set, predefined note is valid for every type of the element.

.. raw:: latex

    \newpage

.. index:: ! Checklist (Definition)

.. _checklist-definition:

Checklists
----------

It is possible to define checklist forms for each type of element.

When a checklist form exists for a given element, the checklist is available for the element.

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the checklist definition.
   * - Element
     - The elements the checklist applies to.
   * - Type
     - Type of the elements the checklist applies to.
   * - :term:`Closed`
     - Flag to indicate that checklist definition is archived. 

.. rubric:: Section: Checklist lines

A checklist is built from checklist lines.

* Click on |buttonAdd|  to create a new checklist line. 
* Click on |buttonEdit| to update an existing checklist line.
* Click on |buttonIconDelete| to delete the corresponding checklist line.

.. figure:: /images/GUI/BOX_ChoicesForChecklistLines.png
   :alt: Dialog box - Choices for the checklist lines 
   :align: center


.. tabularcolumns:: |l|l|

.. list-table:: Fields - Choices for the checklist lines
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Name
     - Name of the subject of the checklist line.
   * - Sort order
     - Order of the line in the list.
   * - Choice #n
     - Possible choices (checks) for the list (up to 5 choices).
   * - Exclusive
     - Are the choices exclusive (select one will unselect others).

.. topic:: Details of dialog box

   * Each line has a name, an order and up to 5 check choices.
   * A line with no check choice will be displayed as a **section title**.
   * Name and Choices have 2 fields : 

     1. Displayed caption. 
     2. Help text that will be displayed as tooltip.

   * Checks can be exclusive (select one will unselect others) or not (multi selection is then possible).





.. raw:: latex

    \newpage

.. index:: ! Email (Receivers)
.. index:: ! Internal alert (Receivers)

.. _receivers-list:

Receivers list
--------------

Receivers can receive email and alert.

A description of receivers below.

.. rubric:: Requestor

* The contact defined as :term:`requestor` on current item; sometimes appears as “contact” (on quotation and order, for instance) and sometimes have no meaning (for instance for milestone).

.. rubric:: Issuer

* The user defined as :term:`Issuer`.

.. rubric:: Responsible

* The resource defined as :term:`responsible`.

.. rubric:: Project team

* All resources affected to the project.

.. rubric:: Project leader

* The resource(s) affected to the project with a “Project Leader” profile.

.. rubric:: Project manager

* The resource defined as the manager on a project.

.. rubric:: Assigned resource

* All resources assigned.

.. rubric:: Other

* Provides an extra field to manually enter email addresses.
* If “other” is checked, an input box is displayed to enter a static mail address list.
* Several addresses can be entered, separated by semicolon.

