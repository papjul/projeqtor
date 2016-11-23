.. raw:: latex

    \newpage

.. title:: Document directories management

.. index:: ! Document (Directories management) 

.. _document-directory:

Document directories
--------------------

Document directories management allows to define a structure for document storage.

* The files of document will be stored in the folder defined by the parameters  «Document root» and «Location».
* «Document root» is defined in :ref:`Global parameters <document-section>` screen. 

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table:: 
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the directory.
   * - **Name**
     - Name of the directory.
   * - Parent directory
     - Name of the parent directory to define hierarchic structure.
   * - Location
     - Folder where files will be stored.
   * - Project
     - Directory is dedicated to this project.
   * - Product
     - Directory is dedicated to this product.
   * - Default type
     - Type of document the directory is dedicated to.
   * - :term:`Closed`
     - Flag to indicate that directory is archived.
 
**\* Required field**

.. topic:: Field: Parent directory

   * The current directory is then a sub-directory of parent.

.. topic:: Field: Location

   * Location is automatically defined as «Parent directory» / «Name».

.. topic:: Field: Project

   * This project will be the default to new stored documents in this directory.

.. topic:: Field: Product

   * This product will be the default to new stored documents in this directory.
   * If the project is specified, the list of values contains the products linked the selected project.
   * If the project is not specified, the list of values contains all products defined.

.. topic:: Field: Default type

   * This document  type  will be the default to new stored documents in this directory.
