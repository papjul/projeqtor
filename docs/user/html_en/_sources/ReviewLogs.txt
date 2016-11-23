.. include:: ImageReplacement.txt

.. title:: Review logs

.. index:: ! Meeting 

.. _meeting:

Meetings
========

Meeting items are stored to keep track of important meetings during the project lifecycle :

* Progress Meetings
* Steering committees
* Functional workshops

In fact, you should keep track of every meeting where decisions are taken, or questions answered.

This will provide an easy way to find back when, where and why a decision has been taken.

.. contents:: Meeting features
   :local: 
   :backlinks: top

.. rubric:: Project task

* Meeting is a task.
* You can assign project resources (named attendees).
* You have progress section that allows for followed resources work and cost. 

.. sidebar:: Other sections


   * :ref:`Attendees<attendees-section>`
   * :ref:`Progress<progress-section-meeting>`
   * :ref:`Predecessor and Sucessor element<predSuces-element-section>`
   * :ref:`Linked element<linkElement-section>`   
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
     - Unique Id for the meeting.
   * - Name
     - Short description of the meeting.
   * - **Meeting type**
     - Type of meeting. 
   * - **Project**
     - The project concerned by the meeting.
   * - **Meeting date**
     - Date of the meeting (initially expected date), including start and end time.
   * - Location
     - Place (room or else) when meeting will stand.
   * - :term:`Description`
     - Description of the meeting. 
 
**\* Required field**

.. topic:: Field: Name

   * If not set, will automatically be set to meeting type completed with meeting date.

.. hint:: Description can be used to store agenda.

.. rubric:: Button: Email invitation

* Allows to send the email to attendees.
* They will receive the invitation in their calendar management tool. 


.. raw:: latex

    \newpage


.. rubric:: Section: Treatment

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Parent activity
     - Parent activity of the meeting.
   * - **Status**
     - Actual :term:`status` of the meeting.
   * - :term:`Responsible`
     - Resource who is responsible for the organization of the meeting.
   * - :term:`Handled`
     - Flag to indicate that meeting has been taken into account. 
   * - :term:`Done`
     - Flag to indicate that meeting has been held.
   * - :term:`Closed`
     - Flag to indicate that meeting is archived.
   * - Cancelled
     - Flag to indicate that meeting is cancelled.
   * - :term:`Minutes<Result>`
     - Minutes of the meeting.
 
**\* Required field**

.. topic:: Field: Parent activity

   * In the WBS structure, under which the meeting will be displayed in the Gantt planning.

.. topic:: Minutes

   * You can enter here only a short summary of the minutes and attach the full minutes as a file.






.. raw:: latex

    \newpage


.. index:: ! Meeting (Periodic)  

.. _periodic-meeting:

Periodic meetings
-----------------

Periodic meeting is a way to define some meetings that will occur on a regular basis.


.. note::

   * Most fields fit, but some information is not present for periodic meetings, such as Minutes or Status. 
   * It is because these fields won’t be set through periodic meeting definition, but must be set directly on the meetings.

.. rubric:: Periodic meeting process

* When saving periodic meeting, elementary meetings are automatically created.
* Changes can be done in elementary meetings. In most cases, these changes won't be erased by periodic meeting updates.

.. topic:: Update on a periodic meeting

   * On each update of a periodic meeting, meetings are re-evaluated.
   * This may lead to deletion of some meetings.
   * This will also reposition meetings, even if their planned dates were elementary updated.

.. rubric:: Attendees assignment

* Attendees can be defined on a periodic meeting.
* They will be copied on the elementary meetings. 
* The periodic meetings will not be planned, only elementary meetings will be. 
* So left work will always be set to zero on periodic meetings.  

.. sidebar:: Other sections

   * :ref:`Attendees<attendees-section>`
   * :ref:`Progress<progress-section-meeting>`
   * :ref:`Predecessor and Sucessor element<predSuces-element-section>`
   * :ref:`Notes<note-section>`   

.. raw:: latex

    \newpage

.. rubric:: Section: Description

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - :term:`Id`
     - Unique Id for the periodic meeting.
   * - Name
     - Short description of the meeting.
   * - **Meeting type**
     - Type of meeting. 
   * - **Project**
     - The project concerned by the meeting.
   * - Location
     - Place (room or else) when meeting will stand.
   * - :term:`Description`
     - Description of the meeting.
 
**\* Required field**

.. rubric:: Section: Treatment

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - Parent activity
     - Parent activity of the meeting.
   * - :term:`Responsible`
     - Resource who is responsible for the organization of the meeting.
   * - :term:`Closed`
     - Flag to indicate that periodic meeting is archived.

.. rubric:: Section: Periodicity

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - **Period**
     - Start date and end date or number of occurrences to define the range of the periodicity.
   * - Time
     - Start and end time for all the meetings.
   * - **Periodicity**
     - Frequency of the meeting, on proposed bases (daily, weekly monthly).
   * - Only on open days
     - Specify that meetings will not be set on off days.

**\* Required field**

.. topic:: Field: Periodicity

   * Several periodicity is proposed:
    
     * Every day
     * Same day every week
     * Same day every mouth
     * Same week every mounth




.. raw:: latex

    \newpage

.. _attendees-section:

Attendees section
-----------------

This section allows to define the list of attendees to the meeting.

.. rubric:: Attendee list

* Meeting is a task you can assign project resources.
* A possibility to assign work to some attendees (project resources). So meeting works of these attendees are booked in the project. 
* More detail about how assigned project resources, see: :ref:`assignment-section` section.
   
.. rubric:: Other attendees

* Extra list of persons attending (or expecting to attend) the meeting, in completion to resource in the attendee list.

.. topic:: Attendees entry

   * You can enter attendees by email address, resource or contact name, user name or initial without caring about. 
   * Just separate attendees with commas or semicolons.

.. note::

   * Duplicate email addresses in the attendee list will automatically be removed.



.. raw:: latex

    \newpage


.. index:: ! Decision

.. _decision: 

Decisions
=========

Decisions are stored to keep track of important decisions, when, where and why the decision was taken.

You can link a decision to a meeting to rapidly find the minutes where the decision is described.

.. sidebar:: Other sections

   * :ref:`Linked element<linkElement-section>`   
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
     - Unique Id for the decision.
   * - **Name**
     - Short description of the decision.
   * - **Decision type**
     - Type of decision. 
   * - **Project**
     - The project concerned by the decision.
   * - :term:`Description`
     - Complete description of the decision.
 
**\* Required field**

.. rubric:: Section: Validation

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - **Status**
     - Actual :term:`status` of the decision.
   * - Decision date
     - Date of the decision.
   * - Origin
     - Origin of the decision.
   * - :term:`Accountable<result>`
     - Resource accountable for the decision.
   * - :term:`Closed`
     - Flag to indicate that decision is archived.
   * - Cancelled
     - Flag to indicate that decision is cancelled.
 
**\* Required field**

.. topic:: Field: Origin

   * It can be either the reference to a meeting where the decision was taken (so also add the reference to the meetings list), or a short description of why the decision was taken. 

.. topic:: Field: Accountable

   * The person who took the decision.

.. raw:: latex

    \newpage

.. index:: ! Question 

.. _question:

Questions
=========

Question are stored to keep track of important questions and answers.

In fact, you should keep trace of every question and answer that have an impact to the project.

The questions can also afford an easy way to track questions sent and follow-up non-answered ones.

This will provide an easy way to find back when, who and precise description of the answer to a question.

Also keep in mind that some people will (consciously or not) be able to change their mind and uphold it has always been their opinion… 

You can link a question to a meeting to rapidly find the minutes where the question was raised or answered.

.. rubric:: Monitoring indicator

* Possibility to define indicators to follow the respect of dates values.

 .. describe:: Respect of initial due date
 .. describe:: Respect of planned due date

.. sidebar:: Other sections

   * :ref:`Linked element<linkElement-section>`   
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
     - Unique Id for the question.
   * - **Name**
     - Short description of the question.
   * - **Question type**
     - Type of question. 
   * - **Project**
     - The project concerned by the question.
   * - :term:`Description`
     - Complete description of the question.
 
**\* Required field**

.. raw:: latex

    \newpage

.. rubric:: Section: Answer

.. tabularcolumns:: |l|l|

.. list-table::
   :widths: 20, 80
   :header-rows: 1

   * - Field
     - Description
   * - **Status**
     - Actual :term:`status` of the decision.
   * - :term:`Responsible`
     - Resource who is responsible for the follow-up of the question.
   * - Initial due date
     - Initially expected date for the answer to the question.
   * - Planned due date
     - Updated expected date for the answer to the question.
   * - Replier
     - Name of the person who provided the answer.
   * - :term:`Handled`
     - Flag to indicate that question has been taken into account. 
   * - :term:`Done`
     - Flag to indicate that question has been answered.
   * - :term:`Closed`
     - Flag to indicate that question is archived.
   * - Cancelled
     - Flag to indicate that question is cancelled. 
   * - :term:`Response<Result>`
     - Complete description of the answer to the question.

**\* Required field**

  

  


