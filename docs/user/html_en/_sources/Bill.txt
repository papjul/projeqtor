.. include:: ImageReplacement.txt

.. raw:: latex

    \newpage

.. contents:: Bill
   :depth: 2
   :backlinks: top
   :local:

.. title:: Bill

.. index:: ! Bill

.. _bill:

Bills
-----

A bill is a request for payment for delivered work.

Billing will depend on billing type defined for the project.

---------------

.. raw:: latex

    \newpage

.. glossary::

   Billing types

----------------

    Each bill is linked to project, a project has a project type, and a project type is linked to a billing type.
    
    So the billing type is automatically defined for the selected project. 
    
    Billing type will influence bill line format.

----------------

    **At terms**

    * A :ref:`term <term>` must be defined to generate the bill, generally following a billing calendar.
    * Used for instance for: **Fixed price projects**.

    **On produced work**

    * No term is needed.
    * The billing will be calculated based on produced work for resources on selected :ref:`activities <activity-price>`, on a selected period.
    * Used, for instance for: **Time & Materials projects**.

    **On capped produced work**

    * No term is needed.
    * The billing will be calculated based on produced work for resources on selected :ref:`activities <activity-price>`, on a selected period. 

    * Used, for instance for: **Capped Time & Materials projects**.

    .. note::

       * Taking into account that total billing cannot be more than project validated work.

    **Manual**
 
    * Billing is defined manually, with no link to the project activity.
    * Used, for instance for: **Any kind of project where no link to activity is needed**.

    **Not billed**

    * No billing is possible for these kinds of projects.
    * Used, for instance for: **Internal projects & Administrative projects**.

---------------

.. note:: Billing report

   * Only bill with at least status "done" will be available for reporting.
   * Before this status, they are considered as a draft.

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
     - Unique Id for the bill.
   * - **Name**
     - Short description of the bill.
   * - **Bill type**
     - Type of bill.
   * - **Project**
     - The project concerned by the bill.
   * - Date
     - Date of the bill.
   * - Payment deadline
     - Payment deadline.
   * - Payment due date
     - Due date for payment (read only).
   * - Customer
     - Customer who will pay for the bill.
   * - Bill contact
     - Contact who will receive the bill.
   * - Recipient
     - Recipient who will receive the payment for the bill.
   * - :term:`Origin`
     - Element which is the origin of the bill.
   * - Billing type
     - Project billing type.

**\* Required field**

.. topic:: Field: Payment due date

   * The value is calculated with date of bill + payment deadline. 

.. topic:: Fields: Customer & Bill contact 
     
   * Automatically updated from project fields.

.. raw:: latex

    \newpage

.. rubric:: Section: Treatment

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - **Status**
     - Actual :term:`status` of the bill.
   * - :term:`Responsible`
     - Resource who is responsible for the bill.
   * - Sent date
     - Date when bill is sent to customer contact.
   * - Send mode
     - Delivery mode.  
   * - :term:`Done`
     - Flag to indicate that the bill has been treated.
   * - :term:`Closed`
     - Flag to indicate that the bill is archived.
   * - Cancelled
     - Flag to indicate that the bill is cancelled.
   * - Amount
     - Total amount of the bill.
   * - % of order
     - Percentage of the bill balance over order amount. 
   * - Payment
     - Payment of bill.
   * - :term:`Comments<Description>`
     - Comments for the bill.


**\* Required field**

.. topic:: Fields: Amount

   Columns:

   * **Ex VAT**: Amount without taxes.
     
     * The value is automatically updated with the sum of bill line amounts. 

   * **Tax**: Applicable tax. 

     * Automatically updated from customer field.  

   * **Full**: Amount with taxes.

.. topic:: Fields: Payment

   Columns:

   * **Date**: Date of payment.
   * **Amount**: Payment amount.   
   * **Complete**: Flag to indicate that complete payment. 



.. raw:: latex

    \newpage

.. rubric:: Section: Bill lines

Input for each bill line depends on billing type.

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the bill line.
   * - N°
     - Number of the line for the bill.
   * - Description
     - Description of the line.
   * - Detail
     - Detail of the line.
   * - Unit price
     - Unitary price of billed element.
   * - Quantity
     - Quantity of billed element.
   * - Sum
     - Total price for the line (Price x Quantity).
 
.. rubric:: Bill lines management

* Click on |buttonAdd| to add a bill line. A different “Bill line” dialog box will be displayed depends on billing type. 
* Click on |buttonEdit| to modify an existing bill line.
* Click on |buttonIconDelete| to delete the bill line.


.. raw:: latex

    \newpage

.. rubric:: Bill line: At terms

.. figure:: /images/GUI/BOX_BillLineAtTerms.png
   :alt: Dialog box - Bill line - At terms 
   :align: center

   Bill line - At terms

.. tabularcolumns:: |l|l|

.. list-table:: Fields - Bill line - At terms
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - N°
     - Number of the line for the bill.
   * - **Term**
     - Project terms to be selected.
   * - Description
     - Description of line.
   * - Detail
     - Detail of the line.
   * - Price
     - Real amount of term.

**\* Required field**

.. topic:: Field: Description
 
   * Automatically set with the term name.
   * Can be modified on update.

.. topic:: Field: Detail

   * Can be set on update.









.. raw:: latex

    \newpage


.. rubric:: Bill line: On produced work & On capped produced work

.. figure:: /images/GUI/BOX_BillLineOnProduceWork.png
   :alt: Dialog box - Bill line - On produced work & On capped produced work
   :align: center

   Bill line - On produced work & On capped produced work

.. list-table:: Fields - Bill line - On produced work & On capped produced work
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - N°
     - Number of the line for the bill.
   * - **Resource**
     - Project resources to be selected.
   * - **Activity price**
     - Project activities price to be selected.
   * - **Start date**
     - Start date of the period to take into account.
   * - **End date**
     - End date of the period  to take into account.
   * - Description
     - Description of line.
   * - Detail
     - Detail of the line.
   * - Price
     - Price of the activity.
   * - Quantity
     - Quantity of element.
   * - Amount
     - Amount for the line (Price x Quantity).
 


**\* Required field**

.. topic:: Field: Description
 
   * Automatically set with selected resource, activity price name and dates.
   * Can be modified on update.

.. topic:: Field: Detail

   * Can be set on update.


.. raw:: latex

    \newpage

.. _manual-billing:

.. rubric:: Bill line: Manual billing

.. figure:: /images/GUI/BOX_BillLineManual.png
   :alt: Dialog box - Bill line - Manual billing 
   :align: center

   Bill line - Manual billing

.. tabularcolumns:: |l|l|

.. list-table:: Fields - Bill line - Manual billing
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - N°
     - Number of the line.
   * - Amendment
     - Flag to indicate this is an amendment line.
   * - Description
     - Description of the line.
   * - Detail
     - Detail of the line.
   * - Price
     - Unitary price of element / measure unit.
   * - Quantity
     - Quantity of element.
   * - Amount
     - Amount for the line (Price x Quantity).

.. topic:: Field: Amendment 
     
   * This field is used for amendment values in order detail.
 

.. raw:: latex

    \newpage

.. index:: ! Bill (Term)

.. _term:

Terms
-----

A term is a planned trigger for billing.

You can define as many terms as you wish, to define the billing calendar.

.. note::

   * Terms are mandatory to bill “Fixed price” project.
   * A term can be used just one time. The bill name will be displayed.

.. rubric:: A term has triggers

* You can link the activities that should be billed at this term.
* A summary of activities is displayed for validated and planned amount and end date.
* Validated and planned values play the role of reminder.
* You can use these values to set real amount and date.


.. sidebar:: Other sections

   * :ref:`Notes<note-section>`   

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the term.
   * - **Name**
     - Short description of the term.
   * - **Project**
     - The project concerned with the term.
   * - Bill
     - Bill name that uses this term.
   * - :term:`Closed`
     - Flag to indicate that term is archived

**\* Required field**

.. rubric:: Section: Fixed price for term

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Real amount
     - Defined amount for the term.
   * - Real date
     - Defined date for the term.
   * - Validated amount 
     - Sum of validated amounts of activities defined as triggers **(Read only)**.
   * - Validated date
     - Max of validated end dates of activities defined as triggers **(Read only)**.
   * - Planned amount
     - Sum of planned amounts of activities defined as triggers **(Read only)**.
   * - Planned date
     - Max of validated end dates of activities defined as triggers **(Read only)**.

.. topic:: Fields: Amount and Date (Planned & Validated)

   * When a trigger is entered, the values of planned and validated are automatically updated with the sum and the max of triggered amounts.

.. rubric:: Section: Trigger elements for the term

This section allows to manage element trigger.

.. rubric:: Trigger element management

* Click on |buttonAdd| to add an element trigger. 
* Click on |buttonIconDelete| to delete an element trigger.

.. figure:: /images/GUI/BOX_AddTriggerElementToTerm.png
   :alt: Dialog box - Add a trigger element to term 
   :align: center


.. list-table:: Fields - Add a trigger element to term dialog box
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Linked element type
     - Type of element to be selected (Activity, Meeting, Milestone, Project, Test session).
   * - Linked element
     - Item to be selected.

.. raw:: latex

    \newpage

.. index:: ! Bill (Activity Price)

.. _activity-price:

Activities prices
-----------------

Activity price defines daily price for activities of a given **activity type** and a given **project**.

This is used to calculate a billing amount for billing type **On produced work** and **On capped produced work**.

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the activity price.
   * - Name
     - Short description of the activity price.
   * - **Project**
     - The project concerned with the activity price.
   * - **Activity type**
     - Type of activities concerned with the activity price.
   * - Price of the activity
     - Daily price of the activities of the given activity type and the given project.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate that activity price is archived.

**\* Required field**

.. raw:: latex

    \newpage

.. index:: ! Bill (Payment)
.. index:: ! Payment

.. _payment:

Payments
--------

Allow to define payment of bills. 

The bill keeps track of payment.

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the payment.
   * - **Name**
     - Short description of the payment.
   * - **Payment type**
     - Type of payment.
   * - Description
     - Description of the payment.

**\* Required field**


.. rubric:: Section: Treatment

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - **Payment mode**
     - The mode of payment.
   * - **Payment date**
     - Date of payment.
   * - **Payment amount**
     - Amount of the payment.
   * - Payment fee
     - Payment of the fee.  
   * - Payment credit
     - Balance of payment amount less payment fee.
   * - Bill
     - Bill concerned with the payment.
   * - Bill reference
     - Reference of bill.
   * - Customer
     - Customer of bill.
   * - Recipient
     - Recipient of bill. 
   * - Bill amount 
     - Amount of bill.
   * - :term:`Closed`
     - Flag to indicate that payment is archived.


**\* Required field**









