.. include:: ImageReplacement.txt

.. title:: Tickets

.. index:: ! Ticket 

.. _ticket:
.. _simple-ticket:

Tickets
-------

A ticket is a kind of task that can not be unitarily planned. 

It is generally a short time activity for a single ticket, that is interesting to follow unitarily to give a feedback to the issuer or to keep trace of result. 

It can be globally planned as a general activity, but not unitarily.

For instance, bugs should be managed through tickets : 

* You can not plan bugs before they are registered.
* You must be able to give a feedback on each bug.
* You can (or at least should) globally plan bug fixing activity. 

.. index:: ! Ticket (Simple) 

.. topic:: Tickets (simple) 

   * This screen is a limited version of screen "Tickets".
   * It's dedicated to users who want to create and follow their own tickets without be involved in their treatment.
   * When fields and features are available, the description is similar.

.. rubric:: Planning activity

* Planning activity field allows to link the ticket with a planning activity.
* Work on the ticket will be included in this activity.

 .. compound:: **Put the real work from tickets to the resource timesheet**

    * When a resource has entered the real work on the ticket and the ticket is linked to a planning activity.
    * The resource is automatically assigned to this activity.
    * Real work set on tickets is automatically set in resource timesheet.

.. raw:: latex

    \newpage

.. rubric:: Restrict the entry of real work in the ticket.

* Possibility to define that only the responsible of ticket can enter real work.
* This behavior can be set in  :ref:`Global parameters<responsible-section>` screen.

-----------

.. rubric:: Due dates

* Initial and planned due date allows to define a target date for solving the ticket.

 .. compound:: **Initial due date**

    * If a definition of ticket delay exists for giving ticket type and urgency the date is automatically calculated with this delay.
    * Else date is initialized to current day.
    * :ref:`delay-for-ticket` screen allows to define ticket delay.

 .. compound:: **Planned due date**

    * Is used to define a target date after evaluation.
    * Automatically initialized to the initial due date.

 .. compound:: **Monitoring indicator**

    * Possibility to define indicators to follow the respect of dates values.

    .. describe:: Respect of initial due date/time
    .. describe:: Respect of planned due date/time

   

-----------

.. rubric:: Product, component and versions fields

* Allows to identify the product and component relating to the issue.
* Identifies from which versions, the issue occurs and to which versions a resolution will be applied. 

 .. compound:: **Versions identified**

    * A ticket can identify all versions affected.
    * Possibility to define a main version and the other versions affected.

.. note:: 

   * More detail, see: :ref:`Product concept<product-concept>`.


-----------

.. rubric:: Responsible of product

A responsible can be defined for a product or component.

If a product or component is selected, the responsible defined can be automatically assigned to the ticket.

.. note:: Global parameter: Ticket responsible from product responsible

   * This parameter allows to define, if the defined responsible is automatically assigned to the ticket or not.

.. raw:: latex

    \newpage

.. sidebar:: Other sections

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
     - Unique Id for the ticket.
   * - **Name**
     - Short description of the ticket.
   * - **Ticket type**
     - Type of ticket.
   * - **Project**
     - The project concerned by the ticket.
   * - :term:`External reference`
     - External reference of the ticket.
   * - Urgency
     - Urgency for treatment of the ticket, as requested by the issuer.
   * - :term:`Requestor`
     - Contact at the origin of the ticket.
   * - :term:`Origin`
     - Element which is the origin of the ticket.
   * - Duplicate ticket
     - Link to another ticket, to link duplicate tickets.
   * - Context
     - List of 3 items describing the context of the ticket.
   * - Product
     - The product for which this ticket has been identified.
   * - Component
     - The component for which this ticket has been identified.
   * - Original product version
     - Product versions for which the issue has been identified.
   * - Original comp. version 
     - Component versions for which the issue has been identified.
   * - :term:`Description`
     - Complete description of the ticket.

**\* Required field**

.. topic :: Field: Context

   * Contexts are initialized for IT Projects as “Environment”, “OS” and “Browser”. 
   * This can be easily changed values in :ref:`context` screen.  

.. topic:: Product or component

   * List of values contains the products and components linked the selected project.

.. topic:: Fields: Original product version & Original comp. version

   * The list of values will be filtered depends on the selected value in fields "Product and component".
   * Click on |buttonAdd| to add a other version, see :ref:`multi-version-selection`.


.. rubric:: Section: Treatment

.. tabularcolumns:: |l|l|

.. list-table:: 
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Planning activity
     - Activity where global work for this kind of ticket is planned. 
   * - **Status**
     - Actual :term:`status` of the ticket.
   * - Resolution
     - Ticket resolution.
   * - :term:`Responsible`
     - Resource who is responsible for the ticket.
   * - Criticality
     - Importance of impact on the system, as determined after analysis.
   * - Priority
     - Priority of treatment.
   * - Initial due date
     - Initial target date for solving the ticket.
   * - Planned due date
     - Actual target date for solving the ticket.
   * - Estimated work
     - Estimated workload needed to treat the ticket.
   * - Real work
     - Real workload spent to treat the ticket.
   * - Left work
     - Left workload needed to finish the ticket.
   * - :term:`Handled`
     - Box checked indicates the ticket is taken over.
   * - :term:`Done`
     - Box checked indicates the ticket has been treated.
   * - Solved
     - Box checked indicates the ticket has been solved.
   * - :term:`Closed`
     - Box checked indicates the ticket is archived.
   * - Cancelled
     - Box checked indicates the ticket is cancelled.
   * - Target product version 
     - Product versions for which a resolution of issue will be delivered.
   * - Target comp. version 
     - Component versions for which a resolution of issue will be delivered.
   * - :term:`Result`
     - Complete description of the resolution of the ticket. 
 
**\* Required field**

.. topic:: Field: Priority

   * Automatically calculated from Urgency and Criticality values. See: :ref:`priority-calculation`.
   * Can be changed manually.

.. topic:: Field: Left work

   * Automatically calculated as Estimated – Real.
   * Set to zero when ticket is done.

.. topic:: Field: Solved

   * The box is automatically checked or unchecked, according to the resolution selected.

.. topic:: Fields: Target product version & Target comp. version

   * The list of values will be filtered depends on the selected value in fields "Product and component".
   * Click on |buttonAdd| to add a other version, see :ref:`multi-version-selection`.



.. raw:: latex

    \newpage

.. rubric:: Button: Start/End work

* This button is clock on/off timer.
* If connected user is a resource, he has the possibility to start working on the ticket.
* When work is finished, he will just have to stop the timer.

.. note::

   * Closing the application or starting work on another ticket will automatically stop the current ongoing work.

* The spend time will automatically be converted as real work, and transferred on planning activity if it is set (decreasing left work on the activity). 

.. rubric:: Button: Dispatch

This button allows to dispatch ticket.

.. figure:: /images/GUI/BOX_DispatchWork.png
   :alt: Dialog box - Dispatch work 
   :align: center


* Click on |buttonAdd| to add a line. 

.. tabularcolumns:: |l|l|

.. list-table:: Fields - Dispatch work dialog box
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Date
     - Dispatch date.
   * - Resources
     - Work dispatch to a resource.
   * - Work
     - Planned work to this resource. 

.. raw:: latex

    \newpage

.. _multi-version-selection:

Multi-version selection
"""""""""""""""""""""""

In the version fields, it's possible to set several versions.

.. topic:: Main and other version

   * The version with smaller id will appear in the select list and is considered as the main version.
   * Other versions are listed above. 
   * It is possible to set an ‘other’ version as the main version using the button |iconSwitch|.


* Click on |buttonAdd| to add a other version. 
* Click on |buttonIconDelete| to delete a version.

.. figure:: /images/GUI/BOX_AddOtherVersion.png
   :alt: Dialog box - Add other version 
   :align: center

   
.. _priority-calculation:

Priority value calculation
""""""""""""""""""""""""""

Priority value is automatically calculated from **Urgency** and **Criticality** values.

Priority, Urgency and Criticality values  are defined in lists of values screens. See: :ref:`priority`, :ref:`urgency` and :ref:`criticality` screens.

In the previous screens, a name of value is set with numeric value.  

Priority numeric value is determined by a simple equation as follows:

.. topic:: Equation

   * [Priority value] = [Urgency value] X [Criticality value] / 2
   * For example:

     * Critical priority (8) = Blocking (4) X Critical (8) / 2

.. rubric:: Default values

* Default values are determined.
* You can change its values while respecting the equation defined above. 











