.. include:: ImageReplacement.txt

.. title:: Graphical User Interface

.. raw:: latex

    \newpage

Global view
===========

ProjeQtOr interface is divided into several areas.

Those areas are :

* :ref:`top-bar` |one|
* :ref:`logo-area` |two|
* :ref:`menu-document-window` |three|
* :ref:`message-link-window` |four|
* :ref:`list-window` |five|
* :ref:`detail-window` |six|
* :ref:`info-bar` |seven|

.. figure:: /images/GUI/SCR_GeneralwithArea.png
   :alt: Global view
   :align: center

   Global view



.. raw:: latex

    \newpage

.. rubric:: Area separation

* The splitters allow resizing areas in the interface.
* The green splitter allows to resize the areas **«Menu and Documents window» and «Message and Link window»**.
* The red splitter allows to resize the areas left and right.
* The orange splitter allows to resize the areas **«List window» and «Detail window»**.

.. note::

   * The  position of splitters is saved and retrieved on each connection.

.. figure:: /images/GUI/SCR_WindowsSplitters.png
   :alt: Windows splitters
   :align: center

   Area separation


.. raw:: latex

    \newpage

.. _top-bar:

Top bar
-------

.. figure:: /images/GUI/ZONE_TopBar.png
   :alt: Top bar
   :align: center

   Top bar


.. index:: ! Project (Selector)

.. rubric:: 1 - Project selector

* Allows to select the project to work on.
* Restricted the visibility of all objects of the selected project, including sub-projects if any.
* Defined the “default” project for new items.

.. note:: User parameter: Default project

   * Defines the project that will be selected by default.

.. rubric:: 1.1 - Project selector parameters

* Click on |buttonIconParameter| to display the project selector parameters dialog box, you can select :

  * View closed projects.
  * Change the project list display mode.
  * Refresh the list.

.. figure:: /images/GUI/BOX_ProjectSelectorParameters.png
   :alt: Dialog box - Project selector parameters
   :align: center

.. rubric:: 1.1.a - Standard (reflecting WBS structure)

* List of project and sub-project are displayed according to WBS structure.

.. figure:: /images/GUI/ZONE_ProjectSelectorStandardMode.png
   :alt: Example - Project list display mode standard
   :align: center

.. raw:: latex

   \newpage


.. rubric:: 1.1.b - Filtering select (with autocomplete)
 
* List of project and sub-project are displayed according to entered text.
* Search criteria works in a "starts with" mode
* Autocompletion is active

.. figure:: /images/GUI/ZONE_ProjectSelectorAutoCompleteMode.png
   :alt: Example - Filtering select (with autocomplete)
   :align: center

.. rubric:: 1.1.c - Filtering select (with search)

* List of project and sub-project are displayed according to search criteria.
* Search criteria works in a "contains" mode
* Autocompletion is **not** active

.. figure:: /images/GUI/ZONE_ProjectSelectorSearchMode.png
   :alt: Example - Filtering select (with search)
   :align: center


.. raw:: latex

   \newpage

.. rubric:: 2 - Menu on top bar

Menu on top bar allows a rapid access to elements.

.. figure:: /images/GUI/ZONE_TopBarMenu.png
   :alt: Menu on top bar
   :align: center

   Menu on top bar

.. compound:: **Menu selector** |A|
   
   * The menu selector allows to reduce buttons list displayed |B| in the top bar.
   * The arrows |C| allow to scroll buttons list.
   * Move cursor over the menu selector to display menus list. Click on menu to select it.

.. note:: Predefined menus

   * Predefined menus are available and regroup buttons according to the work context.

.. rubric:: Custom menu

* A user can define its custom menu in the top bar.
* Move cursor over the menu selector and click on "Custom menu" to select it. 

 .. compound:: **Added an item** 

    #. Select a predefined menu (for example: "All menus").
    #. Move the cursor over icon wanted.
    #. Click on the right button, a message appear.
    #. Click on the left button and the icon will be added to the custom menu.

 .. note::

    * A star is added on selected icons.

 .. compound:: **Remove an item** 

    #. Move the cursor over icon (icon with star).
    #. Click on the right button, a message appear.
    #. Click on the left button and the icon will be removed from the custom menu.


.. raw:: latex

   \newpage



.. _navigation-buttons:

.. rubric:: 3 - Navigation buttons

* The navigation buttons |buttonIconBackNavigation| |buttonIconForwardNavigation| give access to previous and next items in the history.

.. rubric:: 4 - Button «New tab» 

* Allows to open a new tab within the same session.


.. raw:: latex

   \newpage

.. _logo-area:

Logo area
---------

.. figure:: /images/GUI/SCR_LogoArea.png
   :alt: Logo area
   :align: center

   Logo area

.. rubric:: Information about the software

* Click on «Logo Area» will display the software information box.

.. figure:: /images/GUI/INFO_SoftwareInformation.png
   :alt: Box - Software information
   :align: center


.. rubric:: Online user manual

* Click on |buttonIconHelp| or use shortcut key :kbd:`F1` will open the online user manual, to the page corresponding to the current screen. 

  .. note:: 

     * You can change logo with your own.
     * Refer to administration guide to replace the logo.


.. raw:: latex

    \newpage


.. _menu-document-window:

«Menu» and «Documents» windows
------------------------------

.. figure:: /images/GUI/ZONE_MenuDocument.png
   :alt: «Menu» and «Documents» windows 
   :align: center

   «Menu» and «Documents» windows

.. note:: Toggling between windows

   * To toggling between windows, just click on window header.  

.. rubric:: 1 - Menu window

* The menu is proposed as a tree view of reachable items. 
* The items presented will depend on the access rights of user to the screens.
* Click on a grouping line will expand-shrink the group.
* Click on an item will display the corresponding screen in the main area (right side of the screen).



.. note:: User parameter “Icon size in menu”

   * This parameter defines the size of icons in the menu.


.. rubric:: 2 - Documents window

* Document directories give direct access to documents contained in the directory.

 .. compound:: **3 - Document directories**

    * This icon |buttonIconDocDir| gives direct access to the :ref:`document-directory` screen.

.. raw:: latex

    \newpage

.. _message-link-window:

«External shortcuts» and «Console messages» windows
---------------------------------------------------

.. figure:: /images/GUI/ZONE_ShortcutMessage.png
   :alt: External shortcuts and Console messages windows
   :align: center

   «External shortcuts» and «Console messages» windows


.. note:: Toggling between windows

   * To toggling between windows, just click on window header.   

.. rubric:: 1 - External shortcuts window

* Display hyperlinks to remote web pages.
* These links are defined as hyperlink attachments on projects.
* Links displayed here depend on the selected project.


.. rubric:: 2 - Console messages window

* Displays information about main actions: insert, update, delete. 
* The timestamp indicates when the action was done.


 .. note::

    * Messages displayed here are not stored and will be flushed when user logout.

.. raw:: latex

    \newpage


.. _list-window:

List window
-----------

.. contents:: List window features
   :depth: 1
   :local: 
   :backlinks: top

.. figure:: /images/GUI/ZONE_ListWindow.png
   :alt: List window
   :align: center

   List window


.. rubric:: 1 - Element identifier

* Displays the element name and the count of items in the list.  
* Each element is identified with a distinctive icon.


.. _rapid-filter:

.. rubric:: 2 - Rapid filter

* Rapid filtering fields are proposed : «Id», «Name» and «Type».

 .. compound:: **Any change on «Id» and «Name»**

    * Will instantly filter data.  
    * Search is considered as «contains», so typing «1» in «Id» will select «1», «10», «11», «21», «31» and so on.

 .. compound:: **Selecting a «Type»**
  
    * Will restrict the list to the corresponding type.

 .. compound:: **Other filter fields**
  
    * Depending on the element, other filter fields will be available.


.. raw:: latex

    \newpage


.. rubric:: 3 - Buttons 

* Click on |buttonIconSearch| to execute a textual search. (See: :ref:`quick-search`)
* Click on |buttonIconFilter| to define an advanced filter. (See: :ref:`advanced-filter`)
* Click on |buttonIconColumn| to define the columns displayed. (See: :ref:`displayed-columns`)
* Click on |buttonIconPrint| to get a printable version of the list.
* Click on |buttonIconPdf| to export it to PDF format.
* Click on |buttonIconCsv| to export data of the selected items in a CSV format file. (See: :ref:`export-csv-format`)
* Click on |buttonIconNew| to create a new item of the element.


.. rubric:: 4 - Checkbox «Show closed items»

* Flag on «Show closed items» allows to list also closed items.


.. rubric:: 5 - Column headers

* Click a column header to sort the list on that column (first ascending, then descending).

.. note:: Sorting is not always on the name displayed 

   * If the sorted column is linked to a reference list with sort order value, the sorting is executed on this sort value.

   .. admonition:: For instance    
     
      * Sorting on the «Status» allows to sort values such as defined in the workflow. 

.. rubric:: 6 - Items list

* Click on a line (any column) will display the corresponding item in the detail window.




.. raw:: latex

    \newpage

.. _quick-search:

Quick search
^^^^^^^^^^^^

The quick search allows to execute a textual search.

.. figure:: /images/GUI/ZONE_QuickSearch.png
   :alt: Zone - Quick search
   :align: center

* Click on |buttonIconSearch| to execute the search.
* Click on |buttonIconUndo| to close the quick search.

.. raw:: latex

    \newpage

.. _advanced-filter:

Advanced filter
^^^^^^^^^^^^^^^

The advanced filter allows to define clause to filter and sort.

* The advanced filter definition dialog box is divided into two sections.

.. figure:: /images/GUI/BOX_AdvancedFilterDefinition.png
   :alt: Dialog box - Advanced filter definition
   :align: center
 
.. rubric:: 1 - Active filter

* This section allows to define the filter and sort clauses.

 .. compound:: **Add criteria**

    * Define the clauses of filter or sort in «Add a filter or sort clause».
    * Select the name of the field, the operator and the value to the clause.
    * Click on |buttonAdd| to add additional criteria. 
    * Click on **OK** button to apply the filter.
    * Click on **Cancel** button to revert to previous filter.

 .. compound:: **Remove criteria**

    * To remove a criteria, click on |buttonIconDelete| on the line.
    * To remove all criteria, click on |buttonIconDelete| on the header.
    * Click on the **Clear** button to reset the active filter.

 .. compound:: **Save active filter**

    * Active filter can be saved to reuse.
    * Fill the filter name and click on |IconSave| to save the filter definition.  


.. raw:: latex

    \newpage

 
.. rubric:: 2 - Saved filters

* This section allows to manage saved filters.

* Click on a saved filter to retrieve its definition.
* Click on |buttonIconDelete| from a saved filter to delete it.
* Click on the **Default** button to define the active stored filter as the default, kept even after disconnection.

.. note:: Default filter

   * The default filter is defined for the user.
   * The filter will be automatically applied in the next connection.

.. rubric:: 3 - Shared filters

* Defined filters can be shared with other users.
* Click on |unShareFilter| to share the selected filter.
* Click on |sharedFilter| to unshare the selected filter.



Shared filters are available for all users in them session.

.. figure:: /images/GUI/BOX_AdvancedFilterDefinitionWithSharedFilter.png
   :alt: Dialog box - Advanced filter definition with shared filter 
   :align: center

   Dialog box - Advanced filter definition with shared filter 


.. raw:: latex

    \newpage

.. rubric:: List of filters

* The filter list allows to select a stored filter.
* To see the filter list, move the cursor over the advanced filter icon.

.. figure:: /images/GUI/TIP_AdvancedFilterList.png
   :alt: Popup menu - Stored filters
   :align: center

* Click on the filter name to apply. |buttonIconActiveFilter|
* Click on **«no filter clause»** to reset the filter. |buttonIconFilter|


.. raw:: latex

    \newpage


.. _displayed-columns:

Displayed columns
^^^^^^^^^^^^^^^^^

This functionality allows to define columns displayed in the list  for this element.

* Click on **OK** button to apply changes.
* Click on **Reset** button to reset the list to its default format. 

 .. note::
   
    * The columns display is defined for the user.
    * The definition will be automatically applied in the next connection.

.. figure:: /images/GUI/BOX_SelectColunmsToDisplay.png
   :alt: Dialog box - Select columns to display 
   :align: center


.. rubric:: Column selection

* Use checkboxes to select or unselect columns to display.

 .. note:: 

    * **Id** and **name** are mandatory fields.
    * They cannot be removed from display.

.. rubric:: Columns order

* Use the selector area |buttonIconDrag| to reorder fields with drag & drop feature.


.. raw:: latex

    \newpage

.. rubric:: Column size 

* Use the spinner button |one| to change the width of the field.
* Width is in % of the total list width.
* The minimum width is 1%. 
* The maximum width is 50%.

 .. note:: Field: Name

    * The width of the field is automatically adjusted so that the total list width is 100%.
    * The width of the field cannot be less than 10%.

 .. warning:: Total width over 100%

    * The total width must be limited to a maximum 100%.
    * The exceedance will be highlighted beside the buttons. |two|
    * This may lead to strange display, over page width, on list, reports and PDF export, depending on the browser.




.. raw:: latex

    \newpage

.. _export-csv-format:

Export to CSV format
^^^^^^^^^^^^^^^^^^^^

This functionality allows to export data of list in a CSV file.

The fields are regrouped and presented in the order as they appear in the item description.

* Click on **OK** button to export data.
* Click on **Cancel** button to close the dialog box.

.. note::

   * The active filter defined will be applied to export data.
   * The CSV exported files can directly be imported through the import functionality. (See: :ref:`import-data`)
   * The export definition is defined for each user. 
   * The same definition can be applied in the next export.

.. figure:: /images/GUI/BOX_Export.png
   :alt: Dialog box - Export 
   :align: center

.. rubric:: Fields selection

* Use the checkbox to select or unselect all fields.
* Click on **«Select list columns»** button to restrict selected fields to the ones that are currently displayed in the list.

.. rubric:: Id or name for references

* For fields that reference another item, you can select to export either the id or the clear name for the referenced item.

.. rubric:: Preserve html format for long texts

* Box checked indicating that HTML tags in a long text field will be kept during export.





.. raw:: latex

    \newpage

.. _detail-window:

Detail window
-------------

.. contents:: Detail window features
   :depth: 1
   :local: 
   :backlinks: top


.. figure:: /images/GUI/ZONE_DetailWindow.png
   :alt: Detail window
   :align: center

   Detail window

.. rubric:: 1 - Item identifier

* Identifies the item with the element name and the item id.
* Each element is identified with a distinctive icon.

.. rubric:: 2 - Creation information

* Information on the item (issuer and creation date) in thumbnail format.
* See: :ref:`thumbnails`.


 .. note::

    * Administrator can change information.

.. raw:: latex

    \newpage

.. rubric:: 3 - Buttons

* Click on |buttonIconNew| to create a new item.        
* Click on |buttonIconSave| or use shortcut key :kbd:`Control-s` to save the changes. 
* Click on |buttonIconPrint| to get a printable version of the details.
* Click on |buttonIconPdf|  to get a printable version of the details in PDF format.
* Click on |buttonIconCopy| to copy the current item. (See: :ref:`copy-item`)        
* Click on |buttonIconUndo| to cancel ongoing changes.      
* Click on |buttonIconDelete| to delete the item.      
* Click on |buttonIconRefresh| to refresh the display.      
* Click on |buttonIconEmail| to send details of item by email. (See: :ref:`email-detail`)
* Click on |buttonIconMultipleUpdate| to update several items in one operation. (See: :ref:`multiple-update`)
* Click on |buttonIconShowChecklist| to show the checklist form. (See: :ref:`checklist`)
* Click on |buttonIconShowHistory| to show history of changes. (See: :ref:`change-history`)


 .. note::

    * Some buttons are not clickable when changes are ongoing.
    * |buttonIconUndo| button is clickable only when changes are ongoing.

 .. warning::

    * When changes are ongoing, you cannot select another item or another menu item. 
    * Save or cancel ongoing changes first.

.. rubric:: 4 - Drop file area

* This area allows to add an attachment file to the item.

  * Drop the file in the area.
  * Or click on the area to select a file.

.. raw:: latex

    \newpage


.. rubric:: 5 - Sections

* The fields are regrouped under a section.
* All sections can be folded or unfolded, clicking on the section title. 

 .. compound:: **Columns**

    * The sections are organized in columns.
    * Number of displayed columns can be defined in user parameters.

 .. compound:: **Common sections**

    * Some sections are displayed on almost all screens. (See : :ref:`common-sections`)  

 .. compound:: **Item count in the list**

    * When the section contains a list, the item count is displayed at right of the header.

    .. figure:: /images/GUI/ZONE_SectionHeader.png
       :alt: Header section
       :align: center

       Header section

 .. compound:: **Thumbnails on items in the list**

    * Thumbnails are displayed on item row to present field values in graphical format.
    * See: :ref:`thumbnails`.

 .. compound:: **Go to selected item**

    * In a list, possibility to go directly to an item by clicking on its fields.
    * Cursor change to |pointingHandCursor| on clickable fields.





.. raw:: latex

    \newpage



.. _copy-item:

Copy item
^^^^^^^^^

* Allows copied an item of the element.
* The options displayed in dialog box depends on whether the element is simple or complex.

.. figure:: /images/GUI/BOX_CopyElement.png
   :alt: Dialog box - Copy element
   :align: center

.. rubric:: Simple element

* Simple element (environment parameters, lists,…) can only be copied “as is”.

.. rubric:: Complex element

* Complex element (Tickets, Activities, …), it is possible to copy them into a new kind of elements.

* For instance, it is possible to copy a Ticket (the request) into an Activity (the task to manage the request). 

.. compound::

   * It is possible to select:

     * New kind of element.
     * Select new type (corresponding to the kind of element).
     * Change the name.
     * Select whether the initial element will be indicated as the origin of the copied one.
     * For main items, it is also possible to choose to copy links, attachments and notes.
     * For Projects and Activities, it is also possible to copy the hierarchic structure of activities (sub-projects, sub-activities). 

.. note:: 
  
   * The new item has the status “copied”.


.. raw:: latex

    \newpage

.. index:: ! Email (Send)
  
.. _email-detail:

Email detail
^^^^^^^^^^^^

Allows to send an informative email to defined recipients list.

.. figure:: /images/GUI/BOX_EmailDetail.png
   :alt: Dialog box - Email detail
   :align: center

.. rubric:: Recipients list

* The list is defined according to the role of the recipient. (See: :ref:`projeqtor-roles`)
* Flag on the role checkbox to define the recipients list.

 .. compound:: **Checkbox “other”**

    * Flag on the checkbox “other” to manually enter email addresses.

.. rubric:: Message

* The message that will be included in the body of the email, in addition to a  complete description of the item.

.. rubric:: Save as note

* Flag on to indicate the email message will be saved as a note.



.. raw:: latex

    \newpage

.. _multiple-update:

Multiple update
^^^^^^^^^^^^^^^

Allows to update several items in one operation.

* The fields that can be updated depends on the element.
* The fields are grouped by section.
* Click on |buttonQuitMultiMode| to quit the multiple mode window.

.. rubric:: Select items

* The selection of items can be done by selecting them in the list window. |one|
* Or use checkboxes to select/unselect all items in the list. |two|
* The count of items selected is displayed. |three|

.. figure:: /images/GUI/ZONE_MultipleModeList.png
   :alt: Multiple mode item selection
   :align: center

   Multiple mode item selection


.. figure:: /images/GUI/ZONE_MultipleMode.png
   :alt: Multiple mode
   :align: center

   Multiple mode window

.. rubric:: Apply updates

* Click on |buttonIconSave| to save updates on selection.
* Click on |buttonIconDelete| to delete all selected items.
* The update will be applied to all the items (if possible) and a report will be displayed on the right. |four|


.. index:: ! Checklist

.. _checklist:

Checklist
^^^^^^^^^

Allows to fill a checklist form.

A checklist is available, whether a checklist form is already defined for the element or the element type.


.. note::

   * The checklist forms are defined in :ref:`checklist-definition` screen.

.. note::

   * The access to view the checklist depends on your access rights.


.. rubric:: Displaying the checklist

* The user parameter «Display checklists» allows to define whether the checklist appears in a section or in a dialog box.
* If the value “On request” is set, the button |buttonIconShowChecklist| appears on the detail header window.
  
  * Click on |buttonIconShowChecklist| to display the checklist form.

* With other value the “Checklist” section appears in the detail window. 

.. figure:: /images/GUI/BOX_Checklist.png
   :alt: Dialog box - Checklist
   :align: center

   Dialog box - Checklist
 
.. figure:: /images/GUI/ZONE_Checklist.png
   :alt: Section - Checklist
   :align: center

   Section - Checklist

.. rubric:: How to use
 
* The user just has to check information corresponding to the situation.
* When done, the user name and checked date are recorded and displayed.
* Each line can get an extra comment, as well as globally on the checklist.


.. raw:: latex

    \newpage

.. index:: ! Change history

.. _change-history:

History of changes
^^^^^^^^^^^^^^^^^^

All the changes items are tracked.

They are stored and displayed on each item.

.. note::

   * On creation, just an insert operation is stored, not all the initial values on creation.

.. tabularcolumns:: |l|l|

.. list-table:: Fields of changes
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Operation
     - The operation on the item (insert or update).
   * - Data
     - The field modified.
   * - Value before
     - The value of the field before the update.
   * - Value after
     - The value of the field after the update.
   * - Date
     - Date of change operation.
   * - User
     - Name of the user who operated the change.


.. rubric:: Displaying the history of changes

* The user parameter «Display history» allows to define whether the history of changes appears in a section or in a dialog box.
* If the value “On request” is set, the button |buttonIconShowHistory| appears on the detail header window.
  
  * Click on |buttonIconShowHistory| to display the history of changes.

* If the value “Yes” is set, the “Change history” section appears in the detail window. 

.. figure:: /images/GUI/BOX_HistoryChange.png
   :alt: Dialog box - History of changes
   :align: center

   Dialog box - History of changes

.. figure:: /images/GUI/ZONE_HistoryChange.png
   :alt: Section - Change history
   :align: center

   Section - Change history


.. raw:: latex

    \newpage

.. rubric:: Show/Hide work

* This button allows to show or hide work changes done in “Real Work Allocation”.
* For section «Change history»  the display of work is defined in  user parameter «Display history».
 
.. raw:: latex

    \newpage

Text editor
^^^^^^^^^^^

Text editors are available for editing of long text fields like description, results, notes, ...


.. note:: Parameter “Editor for rich text”

   * Selection of text editor can be done in User and Global parameters screens.

.. rubric:: CK Editor

* The most advanced web editor.
* Spell checker available with this text editor. 

.. figure:: /images/GUI/ZONE_CKEditor.png
   :alt: CK Editor
   :align: center

   CK Editor


.. rubric:: Dojo Editor

* Historically first used on ProjeQtOr.

.. figure:: /images/GUI/ZONE_DojoEditor.png
   :alt: Dojo Editor
   :align: center

   Dojo Editor

.. raw:: latex

    \newpage

.. rubric:: Inline editor

* As Dojo Editor. 
* Activated only when needed.
* Text zone is extendable.

.. figure:: /images/GUI/ZONE_LongTextFields.png
   :alt: Inline editor
   :align: center

   Inline editor


.. rubric:: Plain text editor

* Conventional text input.
* Text zone is extendable.

.. figure:: /images/GUI/ZONE_PlainTextEditor.png
   :alt: Plain text editor
   :align: center

   Plain text editor






.. raw:: latex

    \newpage

Special fields
^^^^^^^^^^^^^^

This section describes the ProjeQtOr special fields.

.. contents:: Special fields
   :depth: 1
   :local: 
   :backlinks: top


Accelerator buttons
"""""""""""""""""""

.. _moveToNextStatus-button:

.. rubric:: Move to next status button

* This button allows to skip to the next status without having to open the list.
* The next status is defined by the workflow linked to the type of element. 

.. figure:: /images/GUI/BUTTON_MoveToNextStatus.png
   :alt: Button - Move to next status 
   :align: center

.. _assignToMe-button:

.. rubric:: Assign to me button

* This button allows to set the current user in the related field.

.. figure:: /images/GUI/BUTTON_AssignToMe.png
   :alt: Button - Assign to me 
   :align: center



.. raw:: latex

    \newpage


.. _combo-list-fields:

Combo list field
""""""""""""""""

* Combo list field allows to search, view or create item associated with the field.

.. note::

   * The access to view or create item depends on your access rights.
   * Some buttons can be not available.


.. figure:: /images/GUI/ZONE_ComboListFields.png
   :alt: Field - Combo list
   :align: center

* Click on |comboArrowDown| to get the list of values.
* Click on |iconGoto| will directly go to the selected item.

  .. note ::

     * Click on |buttonIconBackNavigation| to return to the last screen. (More detail, see: :ref:`Top bar <navigation-buttons>`)

.. raw:: latex

    \newpage

* Click on |buttonIconSearch| to access item details.
* The next action depends on whether the field value is filled or not.


 .. compound:: **Field value is filled**

    * If the field value is filled, the details of item associated  are displayed.

    .. figure:: /images/GUI/BOX_DetailOfListElement.png
       :alt: Dialog box - Item details
       :align: center

       Dialog box - Item details

    * Click on |buttonIconSearch| to re-select an item.
    * Click on |buttonIconUndo| to close the dialog box.



 .. compound:: **Field value is empty**

    * If the field value is empty, the list of items is displayed, allowing to select an item.

    .. figure:: /images/GUI/BOX_DetailOfListElementList.png
       :alt: Dialog box - List of items 
       :align: center

       Dialog box - List of items

       * Click on |buttonIconSelect| to select items.
       * Click on |buttonIconNew| to create a new item.
       * Click on |buttonIconUndo| to close the window.

    .. note:: Window header

       * You have access to :ref:`Rapid filter<rapid-filter>`, :ref:`Quick search<quick-search>` and :ref:`Advanced filter<advanced-filter>`.

    .. note:: Select several items

       * Some elements is possible to select several items, use :kbd:`Control` or :kbd:`Shift`.
 
 
.. raw:: latex

    \newpage

.. _origin-field:

Origin field
""""""""""""

* This field allows to determine the element of origin.
* The origin is used to keep track of events (ex.: order from quote, action from meeting).
* The origin may be selected manually or automatically inserted during copying an element. 

.. figure:: /images/GUI/ZONE_OriginField.png
   :alt: Field - Origin 
   :align: center


.. rubric:: Origin element

* Click on |buttonAdd| to add a orgin element.
* Click on |buttonIconDelete| to delete the link.

.. figure:: /images/GUI/BOX_AddAnOriginElement.png
   :alt: Dialog box - Add an origin element 
   :align: center

.. tabularcolumns:: |l|l|

.. list-table:: Fields of add an origin element dialog box
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Type of the orign
     - Type of element to be selected.
   * - Origin element
     - item to select.


Set color field
"""""""""""""""

* This field allows to set the color of an item.
* Used to differentiate items in list or report.
* Click on list of colors to select.
* Click on “Reset” button to erase.

.. figure:: /images/GUI/ZONE_SetColor.png
   :alt: Zone - Set color field 
   :align: center


.. raw:: latex

    \newpage

.. _thumbnails:

Thumbnails
""""""""""

Thumbnails are a graphical representation of the field value.

.. rubric:: Date

* Displays the date of creation or update of the item.
* Move cursor over thumbnail to display the date.


 .. compound:: |calendarRed| The Item have been created or updated today.

 .. compound:: |calendarYellow| The Item have been created or updated recently. 

 .. compound:: |calendarWhite| Default view.

.. rubric:: User

* Displays the user who created or updated the item.
* Move cursor over thumbnail to display the name and a large photo of the user.

 .. compound:: |defaultUserThumb| The user doesn't have a photo.


.. rubric:: Comment

* |Note| Indicates a comment or description is defined.
* Move cursor over thumbnail to display text.

.. rubric:: Privacy

Indicates the visibility level defined in a note or attachment.

 .. compound:: |privateThumb| Private contents.

 .. compound:: |teamThumb| Visible to team.


.. rubric:: Color

* Displays a colored circle for field colorable.
* Some list of values has a field to define a color. 
* A color is defined for each value.





.. raw:: latex

    \newpage

.. _info-bar:

Info bar
--------

.. figure:: /images/GUI/ZONE_Infobar.png
   :alt: Info bar zone
   :align: center

   Info bar

.. rubric:: 1 - Log out button

* Allows to disconnect the user.

 .. note:: User parameter “Confirm quit application”

    * This parameter defines whether a  disconnection confirmation will be displayed before.

.. rubric:: 2 - User parameters button

* Allows to access user parameters.

.. rubric:: 3 - Hide and show menu button

* Allows to hide or show the menu section.

 .. note:: User parameter “Hide menu”

    * This parameter defines whether the menu is hidden by default.

.. rubric:: 4 - Switched mode button

* Allows to enable or disable switched mode that allows to switch between list and detail windows.
* Window selected is displayed in "full screen" mode.
* Hidden window are replaced by a gray bar.
* Click on the gray bar to switch between windows. 

 .. note:: User parameter “Switched mode”

    * This parameter defines whether switching mode is enabled or not.

.. rubric:: 5 - Database name

* Display database name.

.. rubric:: 6 - Version button

* Displays the application version.
* Click on button to access to ProjeQtOr site.


.. raw:: latex

    \newpage

.. index:: ! Internal alert (Display)
.. index:: ! Indicator (Internal alert)

Internal alert
==============

Internal alerts can be sent to users.

An internal alert can be sent by the administrator or by monitoring indicators.

 .. compound:: **By the administrator**

    * The administrator can send internal alert by administration console. (See: :ref:`admin-console`)
    * The message will be received by user via message pop-up.

 .. compound:: **Monitoring indicators**   

    * Monitoring indicators send only warning and alert message.
    * The message contains information that explains the alert:

      * Item id and type.  
      * Indicator description. 
      * Target value.
      * Alert or warning value.

    * The indicators are defined in :ref:`indicator` screen.    

.. raw:: latex

    \newpage

.. rubric:: Message pop-up

Users may receive messages pop-up, displayed on the bottom right corner of the screen.

Three kinds of message may be displayed:

* Information
* Warning
* Alert

.. figure:: /images/GUI/BOX_Alert.png
   :alt: Example - message pop-up
   :align: center

.. rubric:: Action on message pop-up

Three possible actions:

* Select to remind you in a given number of minutes (message will close and appear  again in the given number of minutes).
* Mark it as read to definitively hide it. 
* Mark as read all remaining alerts (the number appears on the button).  

.. note::

   * On :ref:`alerts` screen, the user can read the alert messages marked as read.

.. raw:: latex

    \newpage





.. rubric:: Alert on detail window

On indicatorable items, you may see a small icon on top left of the detail of the item.

Just move the mouse over the icon to display, which indicator has been raised.

.. figure:: /images/GUI/ZONE_Alert.png
   :alt: Alert on detail window
   :align: center

   Alert on detail window


.. rubric:: Alert on Today screen

Just move the mouse over the red line to display, which indicator has been raised.

.. figure:: /images/GUI/ZONE_AlertToday.png
   :alt: Alert on Today screen
   :align: center

   Alert on Today screen


.. raw:: latex

    \newpage

Themes
======

Users can select colors Theme to display the interface. 

The new theme is automatically applied when selected.

.. note:: User parameter “Theme”

   * This parameter defines the theme to display.


Multilingual
============

ProjeQtOr is multilingual.

Each user can choose the language to display all the captions.


.. note:: User parameter “Language”

   * This parameter defines the language used to display captions.


Keyboard functionality
======================

.. rubric:: Shortcut keys

* :kbd:`Control-s` to save the changes. 
* :kbd:`F1` to open the online user manual, to the page corresponding to the actual screen.

.. rubric:: Numeric keypad

* The point will be replaced by a comma if the numeric format requires it.


