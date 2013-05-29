BeerTracker - Compact, Efficient and Fast BitTorent Tracker
--------------------------------------------------------------------------------------
Objectives: create a compact tracker code with the small footprint on the CPU, memory and db space.
Short pure procedural code to be small and easy to understand, change and deploy.
The tracker supports ONLY compact announcing and scraping!!! 
Peers IPs and ports stored to db in compact format.
--------------------------------------------------------------------------------------

Prerequisites:
--------------------------------------------------------------------------------------
* HTTP Web Server. Anything that supports PHP.
* PHP 5+, Highly recommend the latest release or 5.4, as code was tested with this release.
* MySQL 5+, Highly recommend the latest release or 5.5, as code was tested with this release.
* Optional:
  * .htaccess & mod_rewrite suppport (use /announce & /scrape without the .php file type extensions).


Installation:
--------------------------------------------------------------------------------------
1) fill out config.php (it contains all of the settings needed to run the tracker, such as path to the database, host, user etc). 

2) copy the files (including the necessary subfolders) to your tracker site. Announce.php, scrape.php at the web root folder
are the ones you have clients call. The actual working announce.php and scrape.php are located at lib/beertracker folders.
These folders and files in there must be properly secured  to prevent direct access by the tracker users, as a precaution. 

Example of the files / folders:

|-- [ web root (acessible) ]
|-- stats.php >----------->--------------
|-- scrape.php >----------->-----------  |
|-- announce.php >--------->--------   | |
|-- [ lib root (inacessible) ]      |  | |
|-- lib/                            v  v v
|-- lib/beertracker/                |  | |
|-- lib/beertracker/peers.sql       |  | |
|-- lib/beertracker/config.php      |  | |
|-- lib/beertracker/announce.php <--   | |
|-- lib/beertracker/scrape.php <---<---  |
|-- lib/beertracker/stats.php <----<-----

As you can see, make sure that where ever you do put the client announce.php and scrape.php, that they point toward the right
location and file (open the file and set the location yourself, its 1 line of code).

3) in MySQL create 'peers' table (choose necessary code for your db engine from peers.sql file).

4) included .htaccess file are to be placed to your web root folder in order to support the typical url 
   format ie. http://tracker.your.site/announce (notice, no .php extension). Not 
   all webservers fully support these files, either because they don't recognize 
   them or because they have them disabled; if you notice them causing any problems 
   just remove them, they're not necessary for successful tracker operation.

5) finished tracker setup.
   * now, you can use the following url for tracking:
     * http://tracker.yoursite.com/announce
   * or the extended url, if your server doesnt support .htaccess files:
     * http://tracker.yoursite.com/announce.php 

Important Links:
--------------------------------------------------------------------------------------
Development Website: http://code.google.com/p/peertracker/
Issue Tracker: http://code.google.com/p/peertracker/issues/list
Source Code Repository: http://peertracker.googlecode.com/svn/trunk/


Misc Credits:
--------------------------------------------------------------------------------------
The creators of OpenTracker, Duga3, BitStorm and PeerTracker. Their code was a great inspiration for this project.
I used bits of their code in my tracker.