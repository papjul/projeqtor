.. title:: Lists of values

.. index:: ! Function - Lists of values

.. _function:

Functions
^^^^^^^^^

The function defines the generic competency of a resource.

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   * - Description
     - Complete description of this value.


**\* Required field**




.. raw:: latex

    \newpage

.. index:: ! Status - Lists of values

.. _status:

Status
^^^^^^

The status is an important element of items lifecycle.

It defines the progress of the treatment of the element.

Some automations are implemented, depending on status definition, to set on items.

.. glossary::

   Handled status

      * This status specifies that the treatment of item is taken over.
      * A :term:`responsible` can be determined.
      * It is possible to require the appointment of a responsible when the status change to "handled".

   Done status

      * This status specifies that the treatment of item is done.
      * A :term:`result` can be specify.
      * It is possible to require a result when the status change to "done".

   Closed status

     * This status specifies that the item is closed.
     * This item is archived, and it disappeared in the list.
     * Item can reappear when "show closed item" is checked.

   Cancelled status

     * This status specifies that the item is cancelled.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - :term:`Handled status`
     - Defines whether ‘handled’ flag is automatically set for this status.
   * - :term:`Done status`
     - Defines whether ‘done’ flag is automatically set for this status.
   * - :term:`Closed status`
     - Defines whether ‘closed’ flag is automatically set for this status.
   * - :term:`Cancelled status`
     - Defines whether ‘cancelled’ flag is automatically set for this status.
   * - Color
     - Color to display the status in element lists.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.

**\* Required field**




.. raw:: latex

    \newpage

.. index:: ! Resolutions - Lists of values

.. _resolution:

Resolutions
^^^^^^^^^^^

Allows to define different kinds of resolution for tickets.

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Solved
     - Box checked indicates the ticket will be automatically marked as “solved” when this resolution is selected.
   * - Color
     - Color to display the resolution in element lists.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   
**\* Required field**


.. index:: ! Quality level - Lists of values

.. _quality-level:

Quality levels
^^^^^^^^^^^^^^

The quality is a manual indicator for the conformity of a project to quality processes.

It defines in a visual way the global conformity of the project.

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Color
     - Color to display the quality level in element lists and on today screen.
   * - Icon
     - Icon that can be displayed for this quality level. 
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   
**\* Required field**

.. topic:: Field: Icon
   
   * If no icon is defined, color is used.
   * You can define your own icons list (see: administration guide).


.. raw:: latex

    \newpage


.. index:: ! Health status - Lists of values

.. _health-status:

Health status
^^^^^^^^^^^^^

The health status is a manual indicator for the health of a project.

It defines in a visual way the global health of the project.

It is displayed on Today screen, for each project, as a Red / Amber / Green traffic light.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Color
     - Color to display the health status in element lists and on today screen.
   * - Icon
     - Icon that can be displayed for this health status.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   
**\* Required field**

.. topic:: Field: Icon
   
   * If no icon is defined, color is used.
   * You can define your own icons list (see: administration guide).



.. index:: ! Overall progress - Lists of values

.. _overall-progress:

Overall progress
^^^^^^^^^^^^^^^^

The overall progress is a manual indicator for global progress of a project.

It defines in a visual way the global progress of the project, independently from work progress.

It is displayed on Today screen, for each project.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.

**\* Required field**


.. raw:: latex

    \newpage


.. index:: ! Trend - Lists of values

.. _trend:

Trends
^^^^^^

The trend is a manual indicator for the global trend of project health.

It defines in a visual way the health trend of the project.

It is displayed on Today screen, for each project.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Color
     - Color to display the trend in element lists and on today screen.
   * - Icon
     - Icon that can be displayed for this trend.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   
**\* Required field**

.. topic:: Field: Icon
   
   * If no icon is defined, color is used.
   * You can define your own icons list (see: administration guide).


.. index:: ! Likelihood - Lists of values

.. _likelihood:

Likelihoods
^^^^^^^^^^^

The likelihood is the probability of a risk or an opportunity to occur.

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Value
     - Value of likelihood.
   * - % value
     - Value in percent.
   * - Color
     - Color to display the likelihood in element lists
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   
**\* Required field**

.. topic:: Field: % value

   * This field is used to calculate a reserve amount according to the likelihood of risk or opportunity.


.. raw:: latex

    \newpage


.. index:: ! Criticality - Lists of values

.. _criticality:

Criticalities
^^^^^^^^^^^^^

The criticality is the importance of an element to its context.

.. topic:: Risk and Opportunity

   * The criticality designs the level of impact the risk or opportunity may have to the project.

.. topic:: Ticket

   * The criticality is the estimated impact that the subject of the ticket may have for the product.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Value
     - Value of criticality.
   * - Color
     - Color to display the criticality in element lists.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   
**\* Required field**



.. index:: ! Severity - Lists of values

.. _severity:
	
Severities
^^^^^^^^^^

The severity designs the level of negative or positive impact the risk or opportunity may have for the product.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Value
     - Value of severity.
   * - Color
     - Color to display the severity in element lists.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   
**\* Required field**


.. raw:: latex

    \newpage


.. index:: ! Urgency - Lists of values

.. _urgency:

Urgencies
^^^^^^^^^

The ticket urgency is an element given by the requestor to indicate the quickness of treatment needed for the ticket.

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Value
     - Value of urgency.
   * - Color
     - Color to display the urgency in element lists.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   
**\* Required field**




.. index:: ! Priority - Lists of values

.. _priority:

Priorities
^^^^^^^^^^

The ticket priority defines the order to treat different tickets.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Value
     - Value of priority.
   * - Color
     - Color to display the priority in element lists.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   
**\* Required field**


.. raw:: latex

    \newpage


.. index:: ! Risk level - Lists of values

.. _risk-level:

Risk levels
^^^^^^^^^^^

The risk level measures the technical risk of implementation of a requirement.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Color
     - Color to display the risk level in element lists.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   
**\* Required field**


.. index:: ! Feasibility - Lists of values

.. _feasibility:

Feasibilities
^^^^^^^^^^^^^

The feasibility defines the first analysis of implementation of a requirement.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this status.
   * - **Name**
     - Name of this status.
   * - Color
     - Color to display the feasibility in element lists.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this status is archived.
   
**\* Required field**



.. raw:: latex

    \newpage


.. index:: ! Efficiency - Lists of values

.. _efficiency:

Efficiencies
^^^^^^^^^^^^

The efficiency measures the result of an action.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Color
     - Color to display the efficiency in element lists.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   
**\* Required field**


.. index:: ! Payment deadline - Lists of values

.. _payment-deadline:

Payment deadlines
^^^^^^^^^^^^^^^^^

The payment deadline is stated on the bill.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Number of days
     - Delay in payment (in days).
   * - End of month
     - Flag to indicate that delay for payment is set at the end of month.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   
**\* Required field**


.. raw:: latex

    \newpage


.. index:: ! Payment method - Lists of values

.. _payment-method:

Payment methods
^^^^^^^^^^^^^^^

The mode of payment.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   
**\* Required field**



.. index:: ! Delivery mode - Lists of values

.. _delivery-mode:

Delivery modes
^^^^^^^^^^^^^^

The mode of delivery.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   
**\* Required field**


.. raw:: latex

    \newpage


.. index:: ! Measure unit - Lists of values

.. _measure-unit:

Measure units
^^^^^^^^^^^^^

The measure units.


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for this value.
   * - **Name**
     - Name of this value.
   * - Plural of name
     - Plural form of name.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate this value is archived.
   
**\* Required field**


