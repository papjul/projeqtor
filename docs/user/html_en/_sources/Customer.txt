.. include:: ImageReplacement.txt

.. raw:: latex

    \newpage

.. contents::
   :depth: 1
   :backlinks: top

.. title:: Customers & Contacts

.. index:: ! Customer
.. index:: ! Bill (Customer) 

.. _customer:

Customers
---------

The customer is the entity for which the project is set.

It is generally the owner of the project, and in many cases it is the payer.

It can be an internal entity, into the same enterprise, or a different enterprise, or the entity of an enterprise.

The customer defined here is not a person. Real persons into a customer entity are called “Contacts”. 

.. sidebar:: Other sections
  
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
     - Unique Id for the customer.
   * - **Customer name**
     - Short name of the customer.
   * - **Type of customer**
     - Type of customer.
   * - Customer code
     - Code of the customer.
   * - Payment deadline
     - The payment deadline is stated on the bill for this customer.
   * - Tax
     - Tax rates that are applied to bill amounts for this customer.
   * - Tax number
     - Tax reference number, to be displayed on the bill. 
   * - :term:`Closed`
     - Flag to indicate that the customer is archived.
   * - :term:`Description`
     - Complete description of the customer.

**\* Required field**

.. rubric:: Section: Address

* Full address of the customer.

.. rubric:: Section: Projects

* List of the projects of the customer.

.. rubric:: Section: Contacts

* List of the contacts known in the entity of the customer.


.. raw:: latex

    \newpage

.. index:: ! Contact (Screen)
.. index:: ! Customer (Contact) 

.. _contact:

Contacts
--------

.. sidebar:: Concepts 

   * :ref:`projeqtor-roles`
   * :ref:`profiles-definition`
   * :ref:`user-ress-contact-demystify`
   * :ref:`photo`

A contact is a person in a business relationship with the company.

The company keeps all information data to be able to contact him when needed.

A contact can be a person in the customer organization.

A contact can be the contact person for contracts, sales and billing.


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
     - Unique Id for the contact.
   * - Photo
     - Photo of the contact.
   * - **Real name**
     - Name of the contact.
   * - User name
     - Name of user.
   * - Initials
     - Initials of the contact.
   * - Email address
     - Email address of the contact. 
   * - Profile
     - Profile of the user.
   * - Customer
     - The customer the contact belongs to.
   * - Function
     - Function of contact.
   * - Phone
     - Phone number of the contact.
   * - Mobile
     - Mobile phone number of the contact.
   * - Fax
     - Fax number of the contact.
   * - Is a resource
     - Is this contact also a resource ?
   * - Is a user
     - Is this contact also a user ?
   * - :term:`Closed`
     - Flag to indicate that contact is archived.
   * - Description
     - Complete description of the contact.

**\* Required field**


.. topic:: Field: Is a resource
   
   * Check this if the contact must also be a resource.
   * The contact will then also appear in the “Resources” list. 

.. topic:: Field: Is a user

   * Check this if the contact must connect to the application. 
   * You must then define the **User name** and **Profile** fields.
   * The contact will then also appear in the “Users” list. 


.. rubric:: Section: Address

Full address of the contact.


.. rubric:: Section: Miscellanous

.. tabularcolumns:: |l|l|

.. list-table:: 
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Don't receive team mails
     - Box checked indicating that the resource doesn't want to receive mails sent to the team.


