.. raw:: latex

    \newpage

.. title:: Affectations

.. index:: ! Affectation

.. _affectation:

Affectations
------------


.. sidebar:: Concepts 

   * :ref:`profiles-definition`
   * :ref:`user-ress-contact-demystify`
   * :ref:`project-affectation`


Allows to manage project affectations.

Offers a global view of affectation.

.. hint::
 
   * You can use filters. 

.. rubric:: Section: Description


.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the affectation.
   * - Resource
     - Name of the affected resource.
   * - Or contact
     - Name of the affected contact.
   * - **Profile**
     - Selected profile.
   * - **Project**
     - Project affected to.
   * - Rate
     - Affectation rate for the project (%).
   * - Start date
     - Start date of affectation.
   * - End date
     - End date of affectation.
   * - :term:`Closed`
     - Flag to indicate that the affectation is archived.
   * - :term:`Description`
     - Complete description of the affectation.

**\* Required field**

.. topic:: Fields: Resource & Contact

   * You can select resource or contact.
   * If none is selected then the user connected is used to define the affectation.
   * If a contact is a resource and inversely, then resource or contact name will be selected too.


