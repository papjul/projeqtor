.. include:: ImageReplacement.txt

.. index:: ! Access rights

.. title:: Access rights

.. index:: ! Access rights (Profile)

.. _profile:

Profiles
--------

.. sidebar:: Concepts 

   * :ref:`profiles-definition`

The profile is a group of authorization and access rights to the data.

Each user is linked to a profile to define the data he can see and possibly manage. 


.. rubric:: Display format

* In the next screens, the name of profiles is displayed in columns.
* Access rights and options are displayed in rows.
* This display format allows to manage easily authorizations for each profile.  
 

-----------------------

.. rubric:: Value of Field "Name"

* The value of field "Name" is not the name displayed, but it is a code in the translation table. 
* The name displayed at right of the field is the translated name.
* See: :ref:`translatable-name`.

.. topic:: New profile

   * The value of field "Name" must be a significant name and must not contain spaces or special characters.
   * Ideally, the value of the field should start with "profile" (to be easily identified in the translation table).


-----------------------

.. raw:: latex

    \newpage

.. sidebar:: Other sections

   * :ref:`Types restrictions<type-restriction-section>`


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table:: 
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the profile.
   * - Name
     - Name of the profile.
   * - Profile code
     - A code that may be internally used when generating emails and alerts.
   * - Sort order
     - Number to define order of display in lists.
   * - :term:`Closed`
     - Flag to indicate that profile is archived.
   * - Description
     - Complete description of the profile.

.. topic:: Field: Profile code

   * ADM:  will designate administrator.
   * PL: will designate project leader. 


.. raw:: latex

    \newpage


.. index:: ! Access rights (Access mode)

.. _access-mode:

Access modes
------------

The access mode defines a combination of rights to read, created, update or delete items.

Each access is defined as scope of visible and updatable elements, that can be :

* **No element:** No element is visible and updatable.
* **Own elements:** Only the elements created by the user.
* **Elements he is responsible for:** Only the elements the user is responsible for.
* **Elements of own project:** Only the elements of the projects the user/resource is affected to.
* **All elements on all projects:** All elements, whatever the project.

-----------------------

.. rubric:: Value of Field "Name"

* The value of field "Name"  is not the name displayed, but it is a code in the translation table. 
* The name displayed at right of the field is the translated name.
* See: :ref:`translatable-name`.

.. topic:: New access mode

   * The value of field "Name" must be a significant name and must not contain spaces or special characters.
   * Ideally, the value of the field should start with "accessProfile" (to be easily identified in the translation table).

-----------------------


.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table:: 
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the access mode.
   * - **Name**
     - Name of the access mode.
   * - **Read rights**
     - Scope of visible items
   * - **Create rights**
     - Scope of possibility to create items.
   * - **Update rights**
     - Scope of updatable items.
   * - **Delete rights**
     - Scope of deletable items.
   * - Sort order
     - Number to define order of display in lists
   * - :term:`Closed`
     - Flag to indicate that access mode is archived.
   * - Description
     - Complete description of the access mode.

**\* Required field**


.. raw:: latex

    \newpage


.. index:: ! Access rights (Access to forms)

.. _access-to-forms:

Access to forms
---------------

This screen allows to define screen access for each profile.

Users belonging to a profile can see the corresponding screen in the menu.

.. rubric:: How to do

* Screens are grouped as seen in the menu.
* Click on checkbox to permit or revoke access to the screen for a profile.

.. figure:: /images/GUI/SCR_AccessToForms.png
   :alt: Access to forms screen
   :align: center

   Access to forms screen

.. index:: ! Access rights (Access to reports)

.. _access-to-reports:

Access to reports
-----------------

This screen allows to define report access for each profile.

Users belonging to a profile can see the corresponding report in the report list.

.. rubric:: How to do

* Reports are grouped by report categories.
* Click on checkbox to permit or revoke access to the report  for a profile.

.. figure:: /images/GUI/SCR_AccessToReports.png
   :alt: Access to reports screen
   :align: center

   Access to reports screen

.. index:: ! Access rights (Access to data - Project dependant)

.. _access-mode-to-data-project-dependant:

Access to data (project dependant)
----------------------------------

This screen allows to set element access mode for each profile.

Allows to define scope of visibility and updating of data in elements for users and resources.

This screen is only for the elements reliant on a project.

.. rubric:: How to do

* For each element, selected the access mode granted to a profile.

.. figure:: /images/GUI/SCR_AccessToDataProjectDependant.png
   :alt: Access to data (Project dependant) screen
   :align: center

   Access to data (Project dependant) screen

.. index:: ! Access rights (Access to data - Not project dependant)

.. _access-mode-to-data-not-project-dependant:

Access to data (not project dependant)
--------------------------------------

This screen allows to set for each profile, elements access rights.

Allows to grant access rights (read only or write) to users, to data on specific elements.

This screen is only for the elements not reliant on a project.

.. rubric:: How to do

* For each element, select the access rights granted to a profile.

.. figure:: /images/GUI/SCR_AccessToDataNotProjectDependant.png
   :alt: Access to data (Not project dependant) screen
   :align: center

   Access to data (Not project dependant) screen

.. index:: ! Access rights (Specific access)

.. _specific-access-mode:

Specific access
---------------

This screen groups specific functionalities options.

Users belonging to a profile can have access to the application specific functions.

Depending on options of functionality, allows to grant access rights, to define data visibility  or to enable or disable option.

.. rubric:: How to do

* For each option, select the access granted to a profile.

.. figure:: /images/GUI/SCR_SpecificAccess.png
   :alt: Specific access screen
   :align: center

   Specific access screen


------------------------

.. rubric:: Section: Real work allocation and Diary

This section allows to:

* Defines who will be able to see and update “real work” for other users.
* Defines who can validate weekly work for resource.
* Defines who have access on diary for resources.

.. note:: Validate real work

   * In most cases, it is devoted to project leader.

.. rubric:: Section: Work and Cost visibility

* This section defines for each profile the scope of visibility of work and cost data.

.. rubric:: Section: Assignment management

* This section defines the visibility and the possibility to edit assignments (on activities or else).

.. index:: ! Checklist (Access rights)

.. rubric:: Section: Display specific buttons

* This section defines whether some button will be displayed or not.

 .. compound:: **Display of combo detail button**

    * This option defines for each profile whether the button |buttonIconSearch| will be displayed or not, facing every combo list box.
    * Through this button, it is possible to select an item and create a new item.
    * This button may also be hidden depending on access rights (if the user has no read access to corresponding elements).

 .. compound:: **Access to checklist**

    * Defines visibility or not to the checklist (if defined).


.. rubric:: Section: Planning access rights

* This section defines access to planning functionality.

 .. compound:: **Calculate planning**

    * This option defines for each profile the ability to calculate planning or not.

 .. compound:: **Access to resource planning of others**

    * This option defines for each profile the ability to see the resource planning of others.

.. rubric:: Section: Unlock items

* This section defines for each profile the ability to unlock any document or requirement.
* Otherwise, each user can only unlock the documents and requirements locked by himself.

.. rubric:: Section: Reports

* This section defines for each profile the ability to change the resource parameter in reports.

.. rubric:: Section: Specific update rights

* Defines for each profile the ability to force delete items.
* Defines for each profile the ability to update creation information.

.. raw:: latex

    \newpage


.. _translatable-name:

Translatable name
-----------------

For profiles and access modes, the value of field "Name" is translatable.

The field "Name" in screens :ref:`profile` and :ref:`access-mode` is not the name displayed, but it is a code in the translation table. 

The name displayed at right of the field is the translated name.

The translated name depends on user language selected in :ref:`User parameters<display-parameters>` screen.

.. note::

   * If translated name is displayed between [], then the value of field "Name" is not found in the translation table.

.. rubric:: Translation table files

* In ProjeQtOr, a translation table file is defined for each available language.
* The files are named "lang.js" and are localized in a directory named with ISO language code.

  * For instance: ../tool/i18n/nls/fr/lang.js.


.. rubric:: How to modify the translation file?

* You can edit  file "lang.js" to add translation of new value or to modify the existing value translation.
* Or, you can download Excel file named "lang.xls", available on ProjeQtOr site. You can modify the translation tables of all languages and produce  files "lang.js".






