.. include:: ImageReplacement.txt

.. raw:: latex

    \newpage

.. title:: Tickets dashboard


.. index:: ! Ticket (Dashboard)

.. _ticket-dashboard:

Tickets dashboard
=================

Allows user to have a tickets global view of his projects.

Shows several small reports, listing the number of tickets by item.

Filters are available to limit scope.

.. rubric:: Direct access to the list of tickets

* In reports, click on an item to get list of tickets corresponding to this item.

.. rubric:: Parameters

* Click on |buttonIconParameter| to access parameters.
* Allows to define reports displayed on the screen.
* Allows to reorder reports displayed with drag & drop feature. Using the selector area button |buttonIconDrag|.

.. note::

   * Arrange reports on left and right on screen. 


.. figure:: /images/GUI/BOX_TicketDashboardParameters.png
   :alt: Dialog box - Ticket dashboard parameters
   :align: center

Filter clauses
--------------

.. note:: Report: Synthesis by status

   * For this report, filter clauses are not applicable.


.. rubric:: Scope filters

* **All issues**
  
  * All tickets.

* **Not closed issues**

  * Tickets not closed. (Status <> 'closed')  	

* **Not resolved issues**

  * Tickets not resolved. (Status <> 'done') 

.. rubric:: Recently updated

* **Added recently**

  * Tickets created within *x* last days.

* **Resolved recently**

  * Tickets treated within *x* last days.

* **Updated recently**

  * Tickets updated within *x* last days.

.. rubric:: Linked to the user 

* **Assigned to me**

  * Tickets that you are responsible for their treatment.

* **Reported by me**

  * Tickets that you are the issuer.

.. rubric:: No resolution scheduled 

* **Unscheduled**

  * Tickets whose resolution is not scheduled in a next product version (target product version not set). 


