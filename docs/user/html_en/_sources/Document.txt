.. include:: ImageReplacement.txt

.. raw:: latex

    \newpage

.. title:: Documents

.. index:: ! Document 

.. _document:

Documents
---------

.. sidebar:: Concepts 

   * :ref:`product-concept`

A document is a reference element that gives a description of a project or product.

Document item describes general information about the document.

The file document will be stored in the tool as versions.

.. topic:: Document files storage

   * Document will always refer to a directory where the file is physically stored.
   * Directories are defined in :ref:`document-directory` screen.

.. topic:: Document versioning

   * Document versioning allows to keep different version at each evolution of the document.
   * Document can evolve and a new file is generated at each evolution.
   * :term:`Type of versioning` must be defined for a document. 

.. topic:: Approval process
   
   * You can define approvers to a document.
   * When all approvers have approved the document version, it is considered as approved and then appears with a check in the list of versions.
   * When creating an approver in the list, the approver is also automatically added to the latest version of the document.
   * When adding a version to the document, the approvers are automatically added to the version.

.. raw:: latex

    \newpage


.. glossary::   

   
   Type of versioning
       |
       A document can evolve following four ways defined as versioning type :

       **Evolutive**

       * Version is a standard Vx.y format. 
       * It is the most commonly used versioning type.
       * Major updates increase x and reset y to zero. 
       * Minor updates increase y.

       **Chronological**

       * Version is a date. 
       * This versioning type is commonly used for periodical documents.
       * For instance : weekly boards.

       **Sequential**

       * Version is a sequential number. 
       * This versioning type is commonly used for recurring documents.
       * For instance : Meeting reviews.

       **Custom**

       * Version is manually set. 
       * This versioning type is commonly used for external documents, when version is not managed by the tool, or when the format cannot fit any other versioning type.


.. raw:: latex

    \newpage

.. sidebar:: Other sections

   * :ref:`Linked element<linkElement-section>`
   * :ref:`Notes<note-section>`

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table:: 
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the document.
   * - **Name**
     - Short description of the document.
   * - **Type**
     - Type of document.
   * - Project
     - The project concerned by the document.
   * - Product
     - The product concerned by the document.
   * - **Directory**
     - Place where the document is stored  to organize document structure. 
   * - Document reference
     - Document reference name.
   * - :term:`External reference`
     - External reference of the document.
   * - Author
     - User or Resource or Contact who created the document. 
   * - :term:`Closed`
     - Box checked indicates the document is archived.
   * - Cancelled
     - Box checked indicates the document is cancelled.

**\* Required field**

.. topic:: Fields: Project and Product

   * Must be concerned either with a project, a product or both.
   * If the project is specified, the list of values for field "Product" contains only products linked the selected project.

.. topic:: Field: Document reference

   * Document reference name is calculated from format defined in the :ref:`Global parameters <format-reference-numbering-section>` screen.

.. topic:: Field: Author

   * Positioned by default as the connected user.
   * Can be changed (for instance if the author is not  the current user).




.. raw:: latex

    \newpage

.. rubric:: Section: Versions

This section allows to manage version list of document.

.. tabularcolumns:: |l|l|

.. list-table:: Version list fields
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - **Versioning type**
     - :term:`Type of versioning` for the document.
   * - Last version
     - Caption of the last version of the document.
   * - :term:`Status`
     - Status of the last version of the document.
 
**\* Required field**



.. rubric:: Version list management

* Click on |buttonAdd| to add a new version. 
* Click on |buttonEdit| to modifiy a version.
* Click on |buttonIconDelete| to delete a version.
* Click on |iconDownload| to download file at this version.

 .. compound:: **Document viewer**

    * Document viewer available for image, text and PDF files.
    * Click on icon.

 .. note:: Name of download file

    * The name of download file will be the document reference name displayed in **description** section.
    * If you want to preserve the uploaded file name, set the parameter in  the :ref:`Global parameters <format-reference-numbering-section>` screen. 


.. figure:: /images/GUI/BOX_DocumentVersion.png
   :alt: Dialog box - Document version 
   :align: center


.. tabularcolumns:: |l|l|

.. list-table::  Fields - Document version dialog box
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - File
     - This button allows to upload locale file.
   * - Last version
     - Caption of the last existing version.
   * - Update
     - Importance of the update concerned by the new version.
   * - New version
     - New caption for the created version.
   * - Date
     - Date of the version.
   * - Status
     - Current status of the version.
   * - Is a reference
     - Check box to set this version is the new reference of the document.
   * - Description
     - Description of the version.

.. topic:: Field: Update

   * A version can have a draft status, that may be removed afterwards.

.. topic:: Field: Is a reference

   * Should be checked when version is validated.
   * Only one version can be the reference for a document.
   * Reference version is displayed in bold format in the versions list.

.. topic:: Field: Description
   
   * May be used to describe updates brought by the version.
   * This icon |Note| appears when the description field is filled.
   * Moving the mouse over the icon will display description text.


.. raw:: latex

    \newpage


.. rubric:: Section: Approvers

This section allows to manage approver list of a document.

.. tabularcolumns:: |l|l|

.. list-table:: Approver list fields
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Id
     - Id of the approver.
   * - Name
     - Name of the approver.
   * - Status
     - Status of the approval of the last version of document.

**Button: Approve now**

 * This button appears in approver list.
 * Just click on the button to approve the latest version of the document.

**Button: Send a reminder email to the approvers**

 * This button allows to send a reminder email to all the approvers who have not yet approved the document.

 
.. rubric:: Approver list management

* Click on |buttonAdd| to add a new approver. 
* Click on |buttonIconDelete| to delete the approver.




.. rubric:: Section: Lock

This section allows to manage document locking.

  **Button: lock/unlock this document**

  * Button to lock or unlock the document to preserve it from being editing, or new version added.
  * When document is locked it cannot be modified.
  * Only the user who locked the document, or a user with privilege to unlock any document, can unlock it.

  **Document locked**
   
  * When a document is locked the following fields are displayed.

  .. tabularcolumns:: |l|l|
  .. list-table:: Fields when the document is locked
     :widths: 20, 80
     :header-rows: 1

     * - Field
       - Description
     * - Locked
       - Box checked indicates the document is locked.
     * - Locked by
       - User who locked the document.
     * - Locked since
       - Date and time when document was locked.
