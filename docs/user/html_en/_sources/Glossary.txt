.. include:: ImageReplacement.txt

Glossary
--------

.. glossary::
  
   Closed

     * Flag to indicate that item is archived.
     * Item will not appear in lists any more, unless “show closed” is checked.

   Description

    * This field allows to define a description on an item.  
    * Depending on the element type, name of field can be different.

    .. seealso:: GUI behavior

       * It is possible to define that description field is mandatory.
       * The element type screens allow to set this parameter to several elements. 
       * More detail, see: :ref:`behavior-section`. 
 
 
  
   Done

    * Flag to indicate that item has been done.
    * Date of done is saved.

   External reference

    * This field allows fill free input.
    * It uses to refer information from an external source.
    * External reference value can be put in email message with **externalReference** special field.

    * More detail, see: **Administration guide**. 
  
   Handled

    * Flag to indicate that item has been taken into account.
    * Date of handling is saved.
    * This generally means that responsible has been named.

   Id

    * Every item has a unique Id, automatically generated on creation.
    * Id is chronologically affected, for all kind of items (Activity, Ticket).
    * Id is shared for all projects and all types (i.e. incident) of the same kind items (i.e. Ticket).
    * **Reference** is displayed after id, automatically generated on creation. 

    * Reference depends on defined format, see under "Format for reference numbering" section in :ref:`global-parameters` screen.
  

    * Default format defines an numbering specific for each project and each type of items.




   Origin

    * Determines the element of origin.
    * The origin is used to keep track of events (ex.: order from quote, action from meeting).
    * More detail, see : :ref:`Origin field<origin-field>`.

   Status

    * The status determines the life cycle of items.
    * It defines the progress of the treatment of the item.
    * Item is linked to a element type, element type is linked to a workflow.
    * A workflow defines the possibility to go from one status to another one, more detail, see: :ref:`workflow`.

    .. seealso:: Accelerator button

       * This button allows to skip to the next status.
       * More detail, see: :ref:`Move to next status button<moveToNextStatus-button>`.    
   
 

 



   Result

    * A result is a description of the treatment of an item.
    * Usually, the result is set when the status of the item is :term:`done<Done status>`.  

    .. seealso:: GUI behavior

       * It is possible to define that result field is mandatory on done status.
       * The element type screens allow to set this parameter to several elements. 
       * More detail, see: :ref:`behavior-section`. 



   WBS

    * Work Breakdown Structure. 
    * Hierarchical position of the element in the global planning.    
       

