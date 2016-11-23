.. ProjeQtOr user guide documentation master file, created by
   sphinx-quickstart on Fri May 29 11:17:53 2015.
   You can adapt this file completely to your liking, but it should at least
   contain the root `toctree` directive.


Welcome
=======

ProjeQtOr is a Quality based Project Organizer, as a web application.

ProjeQtOr focuses on IT Projects, but is also compatible with all kinds of Projects.

Its purpose is to propose a unique tool to gather all the information about the projects. 

The fact is that many Project Management softwares just focus on planning. 
But it is a much too restrictive point of view. 
Of course, planning is an important activity of Project Management and is one of the keys to Project success, 
but it is not the only one.

Project Managers need to foresee all what can happen, measure risks, build an action plan and mitigation plan.

It is also important to track and keep traces of all what is happening to the Project : 
incidents, bugs , change requests, support requests, ...

In this objective, ProjeQtOr gives visibility at all levels of Project Management.

At lower level, the Project follow-up consists in gathering all information, and maintain it up to date. 
This involves all the operational teams.

At upper level, Project Steering uses the follow-up data to take the decisions and build the action plan. 
This allows to bring the adjustments needed to target on the objectives of the project. 

The goal of ProjeQtOr is to be Project Management Method independent. 
Whatever your choice of the method, you can use ProjeQtOr.


.. raw:: latex

    \newpage

What's New in this user guide version?
======================================

This section summarizes significant changes made in the user guide document for this version.

To see complete list of changes made to software, visit the ProjeQtOr web site.

Current version is V5.4. 

.. rubric:: Integration of tickets dashboard

* Tickets dashboard includes several small reports listing the number of tickets.
  
  * See: :ref:`ticket-dashboard`

.. rubric:: Tickets management

* Addition of the "resolution" of the ticket describing the solution provided (or not).
* Addition of "solved" indicator to the ticket.
  
  * See: :ref:`Ticket (Treatment section)<ticket>`
  * See: :ref:`Ticket type (Behavior section) <behavior-section>`

* Management of the list of resolutions.

  * See: :ref:`Resolution (list of values)<resolution>`

* Automatic allocation of the responsible of the ticket when selecting the product or component.

  * See: :ref:`Product (Description section)<product>`
  * See: :ref:`Component (Description section)<component>`
  * See: :ref:`Ticket (Responsible of product)<ticket>`

.. rubric:: Project management 

* Indicator added "under construction" on the project.

  * See: :ref:`project`

.. rubric:: Financial

* Addition of the management of suppliers.

  * See: :ref:`Provider (list of values)<provider>`

* Complement on the project expense object with the same level of information as a bill, to enable purchase management.

  * See: :ref:`individual-expense`
  * See: :ref:`project-expense`

.. rubric:: Planning management 

* Exporting planning  to PDF, identical to the version displayed (with all details and links between tasks).

  * See: :ref:`Export planning to PDF from Gantt chart<gantt-planning>`

.. rubric:: Interfaces

* Ability to research projects in the selector: new list type without auto-complete to search into the name of projects.

  * See: :ref:`Project selector (top bar)<top-bar>`

* Ability to share a filter with other users.

  * See: :ref:`Shared filters (Advanced filter)<advanced-filter>`

* Ability to preview documents for image, text and PDF files.

  * See: :ref:`Document viewer (Attachments section)<attachment-section>`
  * See: :ref:`Document viewer (Document versions section)<document>`

* Addition of the filter for selecting an element in lists of values using the autocomplete.

.. rubric:: Other 

* Allow to manage "private" actions to manage a personal "to-do list".

  * See: :ref:`action`

* Lock the definition of the number of hours per day once real work has been entered.

  * See: :ref:`RWA`

* Explanation about how to create a draft project planning.

  * See: :ref:`Draft planning under planning section (Concept)<planning>`


.. raw:: latex

    \newpage

Features
========

ProjeQtOr  is a "Quality based Project Organizer".

It is particularly well suited to IT projects, but can manage any type of project.

It offers all the features needed to different Project Management actors under a unique collaborative interface.
  
.. toctree::
   :maxdepth: 1
   
   Features

Concepts
========

This chapter defines the concepts of ProjeQtOr.

They can be referred in the following chapters.

.. toctree::
   :maxdepth: 1

   Concept


Graphical user interface
========================

ProjeQtOr provides a very rich user interface.

It may be frightening at first glance because of the very numerous elements it provides, 
but once you'll get familiar to the structure of the interface you'll discover that it is quite simple 
as all screens have the same frames and sections always have simular structure and position.

.. toctree::
   :titlesonly:

   Gui
   CommonSections
   UserParameter



Planning and Follow-up
======================

ProjeQtOr provides all the elements needed to build a planning from workload, 
constraints between tasks and resources availability.

The main activity of Project Leader is to measure progress, analyse situation and take decisions.
In order to ease his work, ProjeQtOr provides several reporting tools, from the well know Gantt chart, to many reports.

.. toctree::
   :maxdepth: 1

   PlanningElements 
   Gantt
   Today 
   Diary
   Report


Real work allocation
====================

As ProjeQtOr implements Effort Driven planning (work drives planning calcuation), 
one of the key to manage project progress is to enter the real work 
and re-estimate left work for all ongoing tasks.

ProjeQtOr provides a dedicate screen for this feature, to ease this input so that entering real work is as quick as possible.
 
.. toctree::
   :titlesonly:

   RealWorkAllocation

Document management
===================

ProjeQtOr integrates an easy to use Document Management feature.

.. toctree::
   :maxdepth: 1

   Document

Ticket management
=================

.. toctree::
   :maxdepth: 1

   Ticket
   TicketDashBoard

  
Requirements & Tests
====================

.. toctree::
   :maxdepth: 1

   RequirementsTest

Financial
=========

.. toctree::
   :maxdepth: 1

   Expense
   Order   
   Bill
   FinancialGallery

Risk & Issue Management
=======================

.. toctree::
   :maxdepth: 1

   RiskIssueManagement

Review logs
===========

.. toctree::
   :maxdepth: 1

   ReviewLogs

Environmental parameters
========================

.. toctree::
   :maxdepth: 1

   ProductVersion
   Affectation
   User
   Resource
   Customer
   Provider
   Recipient
   Context
   DocumentDirectory 

Tools
=====

.. toctree::
   :maxdepth: 1
 
   Tools

Controls & Automation
=====================

.. toctree::
   :maxdepth: 1
 
   ControlAutomation

Access rights
=============

.. toctree::
   :maxdepth: 1
 
   AccessRights

Lists of values
===============

.. toctree::
   :maxdepth: 1

   ListsOfValues


.. _index-element-types-label:

Lists of types
==============

Every element is linked to a type, defining some mandatory data or other GUI behavior.

.. toctree::
   :maxdepth: 1
   
   ListsOfTypes

Plug-ins
========

.. toctree::
   :maxdepth: 1

   Plugin


Administration
==============

.. toctree::
   :maxdepth: 1

   Administration


Glossary
========

.. toctree::
   :maxdepth: 1
  
   Glossary


