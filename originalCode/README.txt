=== Plugin Name ===
Contributors: chuhpl, ManiacalV
Donate link: http://heightslibrary.org/support-your-library/
Tags: meeting room, calendar, library
Requires at least: 3.0.1
Tested up to: 4.9.7
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Book a Room is a library specific meeting room reservation and event calendar system that manages both public and staff events. 

== Description ==
** IMPORTANT! A recent update requires you to add delimiters to your regex. Please check your Locale settings and make sure your phone and zip regex have a slash / at the start and end.**

**Note: You need the [Book a Room Event Calendar plugin](https://wordpress.org/plugins/book-a-room-event-calendar/) to view the Event Calendar.**

== Backup before updating to 2.0 and above. ==

Commercial event calendars can be expensive, confusing for both staff and patrons, and are generally developed with businesses, not libraries, in mind.

**We wanted to develop a system that is designed for libraries!**

The meeting room system we developed and use was designed in-house by our marketing team and coded by our staff developer over the course of about a year. It has improved our workflow, lessened staff stress and frustration, minimized mistakes, and has made our staff and patron's experience a better and more pleasant one.
### Here are a few things we thought about in tackling this project:
* **Time**
     * Whether you're spending your precious off-the-floor time trying to make sense of yesterday's meeting room requests, trying to contact today's reservations after the power (and internet) went out, or are helping a patron sign up over the phone, any unnecessary or excessive time spent dealing with your meeting room system is time away from your other patrons. We started fresh and looked at what we needed as a multiple-branch library system instead of trying to make a cure-all for every situation. Because of this, we were able to simplify our workflow by saving time on managing requests. We also simplified the public forms so that staff spends little to no time handling the questions and problems that a more complex system can create.
* **Price**
    * Since the setup and yearly fees of commercial systems can cost tens of thousands of dollars, we wanted to develop our own system that would be free (outside of our developer's time). We knew from the start that if we could get it to work we wanted to share it with other libraries (or anyone interested) by making it open source and submitting it to the Word Press plugin repository, once we felt we had a finished project. We wanted to make sure that it would be easy to set up, simple to use, and provide a few workflow optimizations to save staff time. Our choice of platform had to be WordPress, an open source and free blogging platform that is one of the most used platforms available; it has a vast amount of plugins, tutorials, themes, and technical support forums available for free online. They also have a great online reference for plugin coding that is always up to date.
* **Bells and/or Whistles**
    * Since we had used other systems for years, we had grown to know what had been causing the bottlenecks in our workflow. We made the public forms easier so staff spend almost no time answering customer questions or fixing mistakes. We simplified the management so that creating, approving, and editing reservations and events is more intuitive. Since it's a WordPress plugin, it uses your own user accounts and doesn't need to be themed separately from your site.
In addition to simplifying our forms, management console, and installation, we added some things that would save addition time, as well as help during a crisis. Daily meetings can be pulled up instantly by date and printed or emailed directly from the page with one click. Lists of contact information for any day's reservations can be pulled up and printed by date quickly and easily. Our forms have error checking that displays the error messages prominently and in English, not error codes. You can even quickly print out room signs that you can tape to your doors.

== Installation ==

Installation requires a few, simple steps after you have initially installed the plugin. You can configure further but these are the simplest things you can do to get things running.

* **Create a page for Reservations and a page for the Event Calendar.**
    * On the Meeting Room Page, the content should be the shortcode **[meetingRooms]**
    * On the Event Calendar (which can run on another sub-domain), use the shortcode **[showCalendar]**
* **Enter the page URLS.**
    * Go to *Meeting Room Settings > Settings*.
    * Add the URL for your Meeting Room page and Event Calendar pages in the appropriate boxes.
    * Add everything after your site's URL including a trailing and leading slash. For instance, if your Meeting Room page is **http://meetings.heightslibrary.org/reserve-a-meeting-room/**, then you would enter **/reserve-a-meeting-room/** into the form.
    * If you are hosting the event calendar on a separate page, please use the complete URL in the Event Calendar box.
* **Enter an Email into the Default Email for Daily reservations.**
    * This email will be the default on the daily reports.
* **Enter pricing.**
    * Enter the deposit and per increment costs for profit and non-profit rooms.
* **Create some amenities.**
    * If you have amenities available, go to the Amenities Admin and create a new amenity. Things that are checked 'Reservable' would be items that can be brought into the room like a TV, Blu-Ray player or Whiteboard. Things that always in the room, like a sink, microwave, fridge or PA system would not have to be reservable.
* **Create a branch**
    * Go to *Meeting Room Settings > Branch Admin.*
    * Add a branch.
        * You must fill in a name, address and map link.
        * Make sure that Available to the public is checked.
        * If you want to be able to schedule events that don't have a specific room, check *Has 'No Location'*
        * For a closed day, do not enter hours. Otherwise, enter start and closing times for the branch.
* **Create a room.**
    * *Rooms are actual, physical spaces. Create one for each space, even if you can partition rooms together to form a larger room.*
    * Go to *Meeting Room Settings > Room Admin.*
    * Click Create a new room.
    * Enter the room name, select the branch and choose any amenities, then submit.
* **Create a room container.**
    * *Room Containers are reservable spaces and are made up of one or more rooms.  If you have a Room A and a Room B, but can also turn them into one large meeting room, you would make 3 containers. One for Room A, one for Room B and one containing both.*
    * Go to *Meeting Room Settings > Containers Admin*.
    * Click Add a new container next to the name of the branch you want this room container to reside.
    * Enter a container name. This can be simple, like "Room A", or more descriptive, like "Room A & B" or "John Doe Room" if the area is named.
    * Enter the max occupancy.
    * Clicking *public* means that this room is available to be reserved by the public, not just for staff events.
    * Clicking *Hide on daily?* will remove it from the daily schedules. This is useful for staff reservable areas like art galleries and display cases that you want to track but not take up space on daily reports or signs.
    * Choose at least one room to be inside the container.
* **Misc. data.**
    * you will need to enter appropriate email addresses and customize the emails on the Email Admin page.
    * You will need to enter your meeting room contract on the Content Admin page.
    * If you have any closings for holidays or other reasons, you can enter them on the Closing Admin screen.
* **Configure the Event Calendar plugin.**
    * Go to Settings > Event Settings. This is in the regular Wordpress settings options.
    * Create a random security key. I usually just mash the keyboard.
    * Enter the database information from the site that you installed the main Book a Room plugin.

This is the minimum required to get the plugin up and running. You can get more information about other settings and features in the rest of this documentation.

== Frequently Asked Questions ==

= How does the "Reserve Buffer" work? =
If today was the 1st of the month and you put a 0 in the reserve buffer, the public, who aren't logged into the site, can reserve rooms at any time after the current time (so nothing in the past).

If you put in a 1, then today is out, but they can reserve all day tomorrow and beyond.

If you put in a 2, then they can't reserve on the 1st or 2nd, but they can on the 3rd, etc.

= Do I need to run both plugins? =

No. The Event Calendar plugin just shows the event calendar. All of the configuration and work is done in this main plugin. We separated them in case you want to install the public facing Event Calendar on a separate subdomain or host.

= Where can I get the Event Calendar plugin? =

You can get it on the Wordpress Repository at the [following link](https://wordpress.org/plugins/book-a-room-event-calendar/).

== Screenshots ==

1. The available rooms list is generated dynamically.

2. The forms are Responsive and easy to use.

== Changelog ==
= 2.8.2.1 =
* One small tweak to the css that got messed up on large screens. (Amenities)

= 2.8.2 =
* Fixed 14 instances of maxlength being typed as maxlengtr.
* Fixed 2 instances of contactState id being incorrect in inputs.
* Added style "resize: vertical" to textarea to help on smaller screens.
* Removed width from textarea css.
* Removed "display: block" from label css.
* Added floated divs around radio buttons.
* Changed inputs to use mobile friendly number/email/tel instead of plain text.
* Most of the changes fix the display and make things easier when using a mobile device.
* Thanks to @speedy_snail for all of the assistance!

= 2.8.1.7 =
* Changed all instances of Event/Organization to Event / Organization to promote better line breaks on smaller screens.

= 2.8.1.6 =
* Removed a line that was causing a session error after activating, logging out, then loggin back in.

= 2.8.1.5 =
* Left the new containers but removed the clearfix css as it was causing more issues than it was fixing.

= 2.8.1.4 =
* Changed public instances of date() to date_i18n() to localize them.

= 2.8.1.3 =
* Missed one value.

= 2.8.1.2 =
* In my effort to format my code on the recent changes, a lot of whitespace was added to the form elements. These whitespaces have been removed.

= 2.8.1.1 =
* Changed the mainForm output for the public form to use output buffering and return the value instead of an return include.

= 2.8.1 =
* Added an outer <div> container to public pages with some CSS to hopefully make things more compatible with certain themes.

= 2.8.0 =
* Changed the INSERT for Events and Meetings to use local time so that it appears correct in the list (as created date).

= 2.7.9 =
* There was an issue with checking if the current user is logged in. Because of this, some days would not allow logged in users to avoid the reserve date limits and would show available times as "*Unavailable"

= 2.7.8.6 =
* Fixed the drop down when editing a single event so Recurrance setting is on *single* and won't throw an error when submitting.

= 2.7.8.5 =
* Removed error checking for reminders since that part of the form is now hidden.

= 2.7.8.4 =
* Changed about 38 date() displays to use the date_i18n()
* Changed several button texts to use _e()

= 2.7.8.3 =
* Several small bug fixes including a capital _E. Changed the conflict in event scheduling from an "auto" form to a list of links allowing the user to view and manage that conflicting instance in a new tab.

= 2.7.8.2 =
* Added in error checking for Titles and Descriptions for events and meetings. Any non-ascii characters entered in from Word will throw an error.

= 2.7.8.1 =
* Attempting to find a bug so I added more granularity to the errors on the submit for Registrations. Adds a new message for all logic errors instead of a single message for all errors.

= 2.7.8 =
* Fixed an error when putting in too high a number for occupancy when requesting a room. This displays properly now.

= 2.7.7.5 =
* Added a missing php to a <? in \templates\events\eventForm_times.php

= 2.7.7.4 =
* The date used to create the calendar on the Staff Events page was using the wrong day and was making the calendar look wrong.

= 2.7.7.3 =
* Fixed a few nested trims() causing an error in PHP 5

= 2.7.7.2 =
* Email fix was throwing a separate error. THis should be fine now.

= 2.7.7.1 =
* A couple of the mail functions weren't adding a "\r\n" after the TO: address, causing the emails to fail. I've added it in.

= 2.7.7 =
* Fixed a problem on the daily view as background colors weren't echoing.

= 2.7.6.6 =
* Updated last changes to offer comma and semicolons between addresses to allow it to work with Outlook.

= 2.7.6.5 =
* I added a new feature that allows you to export to clipboard or open an email with every registrant's address for an event in case you need to contact them.
= 2.7.6.4 =
* Fixed a naming problem with the isSocial drop down that was causing issues on public events with the City field.

= 2.7.6.3 =
* Fixed a bug that kept Internet Explorer from 'Checking All' on the pending page.

= 2.7.6.2 =
* Fixed a bug that was stopping the meeting room contract from displaying.

= 2.7.6 =
* Added an option in each branch to hide the social warning.

= 2.7.5 =
* Misplaced end bracket was hiding rooms if there was a no location available.

= 2.7.4 =
* User @christoyor found a bug. I had <? instead of <?php and it was breaking things. Thanks @christoyor!

= 2.7.3 =
* Got the hours error fixed. Non-logged users were having issues becauise of sessions not pulling the data.

= 2.7.2 =
* Changed ")and" and ")or" to ") and" and ") or" in many parts of the code. Hoping this clears errors up with different versions of PHP

= 2.7.1 =
* Fixed incorrectly closed empty() tags around 416 in /templates/events/eventForm_times.php

= 2.7 =
* Branch table wasn't autoincrementing properly, starting at ID 0. For new installs, this was causing an error on the frist branch made.

= 2.6 =
* First branches were being ignored since they had an ID of 0. Changed empty() to !isset().

= 2.5 =
* Added text domain in for i18n

= 2.4 =
* Several small, incremantal bug releases. Major bug was session tracking for logged out users not allowing them to pick times.

= 2.0 =
* Finished the initial translation conversion. All templates have been changed from html to dynamic PHP with i19n functions added

= 1.7.3 =
* Continuing work on making Booka Room translatable. 

= 1.6.5 =
* I fixed the way that the Reserve Buffer works. If you enter a 0, everything today, after the current time, is available. A 1 means tomorrow is open to reserve. If it's the 1st and you enter a 2, people can schedule meetings on the 3rd and after, etc.

= 1.6.41 =
* Fixed the meeting room contract popup. I changed the size so it centers better.

= 1.6.4 =
* The setup increments weren't showing up as unavailable. Setup increments should now work as Unavailable slots before every shift. THis is so that there is a free slot AFTER each meeting to add cleanup increments.

= 1.6.3 =
* Fixed the thin CSS (again) to display the notes field from the last update properly.

= 1.6.2 =
* On the Search Results and Manage Events pages, when you click on a name that has a number by it, it will take you to the notes attached to that person. I've added an Edit link at the start of each note to that ticket. I've also added code that allows line breaks to work correctly.

= 1.6.1 =
* Added in three options that weren't being deleted on uninstall. bookaroom_defaultState_name, bookaroom_hide_contract, bookaroom_installing

= 1.6 =
* Added an option to hide the meeting room contract.

= 1.5 =
* Added in a function that makes phone numbers look easier to read. Will have to modify this in future releases to scrub all non-numeric characters from numbers before entering into DB
* Got the other event names showing on the list when you edit an event/meeting time.
* Fixed an extra shift being checked off at the end when you go in to edit a time.

= 1.4.1 =
* Removed the "opening shift" forced setup increment which was causing issues and is uneeded.

= 1.4 =
* Replaced a null with array() in bookaroom-meetings-amenities.php:314 to fix a warning when you have no amenities created.

= 1.3 = 
* Added (int) to line 1313 of bookaroom-meetings-public.php. This fixes the mysterious "unavailable" setup spot.

= 1.1 =
* Replaced the jstree javascript libraries. This fixes the issues with the closings page not working for selecting multiple rooms.

= 1.0.30 =
* Added a "parseInt" to the values on the contract popup. The update to Zebra Dialog was ignoring the float values.

= 1.0.28 =
* Removed a "break" in sharedFunctions.php which was throwing an error.

= 1.0.27 =
* Added in a setting and the ability to hide the names of public meetings from the public calendar. When checked, public events on the public calendar show up as "In Use".
* Updated Zebra Dialog, used in the contract popup.

= 1.0.26 =
* Fixed empty amenity bug that made amenities in list show up with an empty one and a comma.
* Added Reply-To Only option and changed email handling. This is so that if you are using an outside email server, you can use reply-to instead of From to help with spam filtering catching "spoofed" emails.
* Added a forced date format to the datepicker so it shows the correct format for the form.

= 1.0.25 =
* Removed the HTTP protocol from the links to jQuery. This was causing an issue on sites using SSL.

= 1.0.24 =
* This small update stops an error from showing up when you try to schedule an event with a time that starts with a 0 (example 01:00 pm instead of 1:00 pm). 

= 1.0.23 =
* Fixed changes from 1.0.22 that were misreporting error times.

= 1.0.22 =
* Added an error message to automatically refortted times.

= 1.0.21 =
* More changes so that simple numeric permalinks work. Fixed issues on small calendar form.

= 1.0.20 =
* More changes so that simple numeric permalinks work. There may be a few remainging problem links, I am huinting them down.

= 1.0.19 =
* Made some changes so that simple numeric permalinks work in calendar.

= 1.0.18 =
* Fixed MySQL FULLTEXT issue with collation that was preventing new installs of the plugin!

= 1.0.17 =
* Searching registrations now also searches exact username (including spaces) so people with short first and last names can be searched.
* Fixed special characters showing up in the Meeting Room Door Signs and on the public side, when they look at a day to schedule.

= 1.0.16 =
* Fixed some documentation

= 1.0.15 =
* Added a zip code and phone number section to the Locale settings that allows users to put in their own regex to validate.

= 1.0.14 =
* Added a link to the name of the person who requested the room and a number next to their name in the search and Pending lists. If there is a number and you click on their name, you will see a popup of all of their notes from all of their previous requests. This should help make it easier to find problem requesters without having to keep a separate notebook or do multiple searches.

= 1.0.13 =
* Removed unused (and erroring on some systems) encrypt and crypt function definitions.

= 1.0.12 =
* Attempt to fix error from 1.0.11 by changing function definitions to public static in new locale functions.

= 1.0.11 =
* Fixed an error caused by hitting certain admin pages before setting up room containers. Fixes errors similar to * Undefined offset: 1 in ####\book-a-room\bookaroom-events.php on line 5168
* Warning: Invalid argument supplied for foreach() in ####\book-a-room\bookaroom-events.php on line 5169* 

= 1.0.10 =
* Added missing files from a bad commit.

= 1.0.9 =
* Added more "international-friendly" address format.

= 1.0.8 =
* Fixed issue with ranges not being found in Closings.

= 1.0.6 =
* Fixed isSocial warning if undefined. Check to see if it's empty before compare.

= 1.0.5 =
* Fixed session bug, again
I used a function that only worked in the latest version of PHP. I replaced it with a method that works in older versions.

= 1.0.4 =
* Fixed session bug
On some installations, there was a bug that would throw an error when logging out or backing up. This corrects that error.

= 1.0 =
* First official release!

