.. include:: ImageReplacement.txt

.. raw:: latex

    \newpage

.. title:: Product & Component

.. _product-component-management:

Product & Component
-------------------

.. sidebar:: Concepts 

   * :ref:`product-concept`

The product and component management is done on screens:

* :ref:`product`
* :ref:`component`
* :ref:`product-version`
* :ref:`component-version`

.. raw:: latex

    \newpage

.. index:: ! Product

.. _product:

Products
^^^^^^^^

Allows to define product and sub-product.

Allows to link components to product.

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
     - Unique Id for the product.
   * - **Name**
     - Name of the product.
   * - Product type
     - Type of product.
   * - Designation
     - Trade name of the product known outside the company.
   * - Customer
     - The customer the product should be delivered to.
   * - Prime contractor
     - The contact, into customer organization, who will be responsible for the product delivery.
   * - Responsible
     - Person responsible for the product.
   * - Is sub-product of 
     - Name of the top product if this product is a sub-product. 
   * - :term:`Closed`
     - Box checked indicates the product is archived.
   * - Description
     - Complete description of the product.

**\* Required field**

.. rubric:: Section: Product versions

* List of versions defined for the product.
* Product versions are defined in :ref:`product-version` screen.

.. rubric:: Section: Composition - List of sub-products used by this product

* List of sub-products for the product.

.. rubric:: Section: Composition - List of components used by this product

* See: :ref:`relationship-product-component`.

.. topic:: Button: Display structure

   * Displays the structure of the product.
   * Box checked "Show versions for all structure" allows to display versions of sub-products and components.
   * Box checked "Show projects linked to versions" allows to display projects linked.


.. raw:: latex

    \newpage

.. index:: ! Product (Version)

.. _product-version:

Product Versions
^^^^^^^^^^^^^^^^

Allows to define versions of a product.

Allows to link a component version to product version.

Allows to link the product version to a project.

.. rubric:: Automatic formatting of version name

* Possibility to define if the version name is automatically produced from the product name and version number.
* Set global parameters to activate this feature.
* Else, the version name will entered manually.

.. sidebar:: Other sections


   * :ref:`Projects linked to this version<Link-version-project-section>`
   * :ref:`Composition - List of component versions used this version<version-product-component>`  
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
     - Unique Id for the version.
   * - **Product**
     - The product on which the version applies.
   * - **Version number**
     - Version number of the product.
   * - **Name**
     - Name of the version.
   * - Prime contractor
     - The contact, into customer organization, who will be responsible for the version delivery.
   * - Responsible
     - Resource responsible of the version.
   * - Entry into service
     - Initial, planned and real entry into service date of the version. 
   * - End date
     - Initial, planned and real end dates of the version.  
   * - Description
     - Complete description of the version.

**\* Required field**


.. topic:: Fields: Version number & Name

   * The field "Version number" appears only if the global parameter "Automatic format of version name" is set to Yes.
   * The field "Name" will be read only.

.. topic:: Field: Prime contractor
     
   * Can be different from product prime contractor.

.. topic:: Field: Entry into service (Real)

   * Specify the date of entry into service.
   * The box "Done" is checked when the real date field is set.

.. topic:: Field: End date (Real)

   * Specify the date end-of-service.
   * The box "Done" is checked when the real date field is set.


.. raw:: latex

    \newpage

.. index:: ! Component

.. _component:

Components
^^^^^^^^^^

Allows to define product components.

Allows to define products using the component.

.. sidebar:: Other sections
   
   * :ref:`Structure - List of products or components using this component<relationship-product-component>`   
   * :ref:`Composition - List of components used by this component<relationship-product-component>`   
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
     - Unique Id for the component.
   * - **Name**
     - Name of the component.
   * - Component type
     - Type of component
   * - Identifier
     - Another name to identify the component.
   * - Responsible
     - Person responsible for the component.
   * - :term:`Closed`
     - Box checked indicates the component is archived.
   * - Description
     - Complete description of the component.

**\* Required field**

.. rubric:: Section: Component versions

* List of versions defined for the component.
* Component versions are defined in :ref:`component-version` screen.


.. raw:: latex

    \newpage

.. index:: ! Component (Version)

.. _component-version:

Component Versions
^^^^^^^^^^^^^^^^^^

Allows to define versions of a component.

Allows to link a product version to component version.

.. rubric:: Automatic formatting of version name

* Possibility to define if the version name is automatically produced from the component name and version number.
* Set global parameters to activate this feature.
* Else, the version name will entered manually.


.. sidebar:: Other sections

   * :ref:`Structure - List of product or component versions using this component version<version-product-component>`  
   * :ref:`Composition - List of component versions used by this version<version-product-component>`  
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
     - Unique Id for the version.
   * - **Component**
     - The component on which the version applies.
   * - **Version number**
     - Version number of the component.
   * - **Name**
     - Name of the version.
   * - Entry into service
     - Initial, planned and real entry into service date of the version. 
   * - End date
     - Initial, planned and real end dates of the version.  
   * - Description
     - Complete description of the version.

**\* Required field**

.. topic:: Fields: Version number & Name

   * The field "Version number" appears only if the global parameter "Automatic format of version name" is set to Yes.
   * The field "Name" will be read only.


.. topic:: Field: Entry into service (Real)

   * Specify the date of entry into service.
   * The box "Done" is checked when the real date field is set.

.. topic:: Field: End date (Real)

   * Specify the date end-of-service.
   * The box "Done" is checked when the real date field is set.


.. raw:: latex

    \newpage

.. _relationship-product-component:

Relationships between product and component elements
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Allows to manage relationships between products and components to define product structure.

See possible relationships:  :ref:`product-structure`   

.. rubric:: Relationships management

* Click on |buttonAdd| to create a new relationship. The dialog box "Structure" appear. 
* Click on |buttonIconDelete| to delete the corresponding relationship.

.. figure:: /images/GUI/BOX_ProductStructure.png
   :alt: Dialog box - Structure 
   :align: center


.. raw:: latex

    \newpage

.. _version-product-component:

Link between versions of products and components
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Allows to define links between versions of products and components.

.. note:: 

   * Only with the elements defined in the product structure.

.. rubric:: Link management

* Click on |buttonAdd| to create a new link. The dialog box "Version Structure" appear. 
* Click on |buttonIconDelete| to delete the corresponding link.

.. figure:: /images/GUI/BOX_VersionStructure.png
   :alt: Dialog box - Version structure 
   :align: center




.. raw:: latex

    \newpage

.. _Link-version-project-section:

Link version to projects
^^^^^^^^^^^^^^^^^^^^^^^^

This section allows to manage links between projects and versions of products.

.. rubric:: Link version to projects management

* Click on |buttonAdd| to create a new link. 
* Click on |buttonEdit| to update an existing link.
* Click on |buttonIconDelete| to delete the corresponding link.


.. figure:: /images/GUI/BOX_ProjectVersionLink.png
   :alt: Dialog box - Project-Version link 
   :align: center


.. list-table:: Fields - Project-Version link dialog box
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Project
     - The project linked to product version or the project list.
   * - Product
     - Product linked to the project or the list of products.
   * - Version
     - Product version linked to the project or list of product versions.
   * - Start date
     - Start date for validity of the link.
   * - End date
     - End date for validity of the link.
   * - Closed
     - Box checked indicates the link is not active anymore, without deleting it.

.. topic:: Fields: Project, Product and Version
 
   * From the screen «Projects», the fields «product and version» will be selectable.
   * From the screen «Product versions», the field «project» will be selectable.




