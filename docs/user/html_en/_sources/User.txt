.. include:: ImageReplacement.txt


.. raw:: latex

    \newpage

.. title:: Users

.. index:: ! User (Definition)

.. _user:

Users
-----

.. sidebar:: Concepts 

   * :ref:`projeqtor-roles`
   * :ref:`profiles-definition`
   * :ref:`user-ress-contact-demystify`
   * :ref:`photo`

The user is a person that will be able to connect to the application.

.. Note:: 

   * To be able to connect, the user must have a password and a user profile defined.

.. rubric:: ProjeQtOr and LDAP users

* ProjeQtOr offers two modes of authentication.

 .. compound:: **ProjeQtOr users**

    * Users' information is kept in the application database.
    * Password policy and login behavior are managed by the application.

    .. note::
       
       * The users "admin" and "guest" are created during installation.


 .. compound:: **LDAP users**

    * Allows users defined in an external directory to login at ProjeQtOr by the LDAP protocol.
    * Users' information and password policy are managed in the external directory.

.. rubric:: Default user profile

* A default user profile is set during creation of the user.
* A different default profile can be set according to mode of authentication.

.. rubric:: Web Service

* ProjeQtOr provides an API to interact with its elements. It is provided as REST Web Service.
* An API key is defined for the user.
* This API key is used to encrypt the data for methods: PUT, PUSH and DELETE.


.. raw:: latex

    \newpage

.. sidebar:: Other sections

   * :ref:`Affectations<affectations-section>`   

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table:: Users description section fields
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the user.
   * - Photo
     - Photo of the user.
   * - **User name**
     - login id of the user.
   * - Real name
     - Name of the user.
   * - Initials
     - Initials of the user.
   * - Email address
     - Email address of the user. 
   * - **Profile**
     - Profile of the user.
   * - Locked
     - Flag used to lock the user, to prohibit connections.
   * - Is a contact
     - Is this user also a contact?
   * - Is a resource
     - Is this user also a resource?
   * - :term:`Closed`
     - Flag to indicate that user is archived.
   * - Description
     - Complete description of the user.

**\* Required field**

.. topic:: Field: User name

   * The user name must be unique.

.. topic:: Field: Is a contact
   
   * Check this if you want created a contact to this user.
   * This user will then appear in the “Contact” list 

.. topic:: Field: Is a resource

   * Check this if you want created a resource to this user.
   * The user will then also appear in the “Resources” list.

.. raw:: latex

    \newpage
 
.. rubric:: Section: Miscellanous

.. topic:: Button: Reset password

   * This button allows to reset password to default password.
   * Default password value is defined in :ref:`Global parameters<user-password-section>` screen.
   * Reset password button is available only for ProjeQtOr users.

.. tabularcolumns:: |l|l|

.. list-table:: Users miscellaneous section fields
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Don't receive team mails
     - Box checked indicating that the resource doesn't want to receive mails sent to the team.
   * - Comes from LDAP
     - Box checked indicating that the user information come from LDAP.
   * - API key
     - Key string used by web service consumer.


.. topic:: Button: Send information to the user

   * This button allows to send by email to the user the login information.



