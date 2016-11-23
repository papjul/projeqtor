.. raw:: latex

    \newpage

.. contents:: Quotation and Order
   :depth: 1
   :backlinks: top
   :local:

.. title:: Quotation and Order

.. index:: ! Quotation 

.. _quotation:

Quotations
----------

A quotation is a proposal estimate sent to customer to get approval of what’s to be done, and how must the customer will pay for it.

On the quotation form, you can record all the information about the sent proposal, including attaching some file completely describing the proposal with details terms and conditions.

.. rubric:: Transform quotation to order

* A quotation can be copied into an order when corresponding document is received as customer agreement.

.. rubric:: Bill lines section

* This section allows to detail the quotation modality.


.. sidebar:: Other sections

   * :ref:`Bill lines<manual-billing>`
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
     - Unique Id for the quotation.
   * - **Name**
     - Short description of the quotation.
   * - **Quotation type**
     - Type of quotation.
   * - **Project**
     - The project concerned by the quotation.
   * - :term:`Origin`
     - Element which is the origin of the quotation.
   * - Customer
     - Customer concerned by the quotation.
   * - Contact
     - Contact in customer organization to whom you sent the quotation.
   * - :term:`Request<Description>`
     - Request description.
   * - Additional info.
     - Any additional information about the quotation.

**\* Required field**

.. topic:: Field: Customer 
     
   * Automatically updated from project field.

.. rubric:: Section: Treatment

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - **Status**
     - Actual :term:`status` of the quotation.
   * - :term:`Responsible`
     - Resource who is responsible for the quotation.
   * - Sent date
     - Date when quotation is sent to customer contact.
   * - Send mode
     - Delivery mode.
   * - Offer validity
     - Limit date of the validity of the proposal.
   * - Likelihood
     - The probability that the proposal will be accepted.
   * - :term:`Handled`
     - Box checked indicates that the quotation is taken in charge.
   * - :term:`Done`
     - Box checked indicates that the quotation is processed.
   * - :term:`Closed`
     - Box checked indicates that the quotation is archived.
   * - Cancelled
     - Box checked indicates that the quotation is cancelled. 
   * - Planned end date
     - Target end date of the activity object of the quotation.
   * - Activity type
     - Type of the activity object of the quotation.
   * - Payment deadline
     - The payment deadline is stated on the quotation.
   * - Amount
     - Total amount of the quotation.  
   * - Estimated work
     - Work days corresponding to the quotation.
   * - Comments
     - Comment about the treatment of the quotation.

**\* Required field**

.. topic:: Field: Payment deadline

   * If the payment deadline is not set, the value defined for the selected customer is used.

.. topic:: Fields: Amount

   Columns:

   * **Ex VAT**: Amount without taxes.
      
     * The amount is automatically updated with the sum of bill lines.

   * **Tax**: Applicable tax. 

     * If the applicable tax isn’t set, the tax defined for the selected customer is used.

   * **Full**: Amount with taxes.

.. hint:: Activity type

   * The activity should be created only after approval.



.. raw:: latex

    \newpage


.. index:: ! Order 

.. _order:

Orders
------

An order (also called command) is the trigger to start work.

On the order form, you can record all the information of the received order.

.. rubric:: Scheduled work and budgeted cost of project

* The scheduled work (field: "validated work") of the project will be initialized with the sum of total work from all orders.
* The budgeted cost (field: "validated cost") of the project will be initialized with the sum of the total amount before taxes for all orders. 
* See: :ref:`progress-section-resource`

.. rubric:: Bill lines section

* This section allows to detail the order modality.

.. sidebar:: Other sections

   * :ref:`Bill lines<manual-billing>`
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
     - Unique Id for the order.
   * - **Name**
     - Short description of the order.
   * - **Order type**
     - Type of order.
   * - Project
     - The project concerned by the order.
   * - Customer
     - Customer concerned by the order.
   * - Contact
     - Contact in customer organization to whom you sent the order.
   * - **External reference**
     - :term:`External reference` of the order (as received).
   * - Date of receipt
     - Receipt date.
   * - Receive mode
     - Delivery mode. 
   * - :term:`Origin`
     - Element which is the origin of the order.
   * - Description
     - Complete description of the order.
   * - Additional info.
     - Any additional information about the order.

**\* Required field**

.. topic:: Field: Customer 
     
   * Automatically updated from project field.

.. rubric:: Section: Treatment

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - **Status**
     - Actual :term:`status` of the order.
   * - :term:`Responsible`
     - Resource who is responsible for the order.
   * - :term:`Handled`
     - Box checked indicates that the order is taken in charge.
   * - :term:`Done`
     - Box checked indicates that the order is processed.
   * - :term:`Closed`
     - Box checked indicates that the order is archived.
   * - Cancelled
     - Box checked indicates that the order is cancelled.
   * - Activity type
     - Type of the activity object of the order.
   * - Linked activity
     - Activity representing the execution of the order.
   * - Initial
     - Initial values.
   * - Amendment
     - Additional values.  
   * - Total
     - Sum of the initial values and amendment.  
   * - Start date
     - Initial start date of the execution of the order.
   * - End date 
     - Initial and validated end date of the execution of the order. 
   * - Comments
     - Comment about the treatment of the order.


**\* Required field**

.. topic:: Fields: Initial, Amendment and Total

   Columns:

   * **Ex VAT**: Amount before taxes.

     * The column value is automatically updated with the sum of bill line amounts.

   * **Tax**: Applicable tax.

     * If the applicable tax isn’t set, the tax defined for the selected customer is used.
 
   * **Full**: Amount with taxes.
   * **Work**: Work days corresponding to the order.
 
     * The column value is automatically updated with the sum of bill lines quantities.
     * When the measure unit is "day". 

.. topic:: Field: Amendment

   * The columns values "Ex VAT" and "Work" are automatically updated with the sum of billing lines with selected amendment checkboxes.

.. topic:: Fields: Start and end date

   * **Initial** : Initial dates
   * **Validated** : Validated dates

.. hint:: Activity type

   * The activity should be created only after approval.


