<?php
// License Information
// * BeerTracker - OpenSource BitTorrent Tracker
// * Revision - 1.0 2013-05-27
// * Copyright (C) Dr.Crane
// *
// * BeerTracker is created under the terms of the GNU General Public License 
// * as published by the Free Software Foundation
// *
// * BeerTracker is distributed WITHOUT ANY WARRANTY; 
// * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
// * See the GNU General Public License for more details.
// * You should have received a copy of the GNU General Public License
// * along with BeerTracker.  If not, see <http://www.gnu.org/licenses/>.
///////////////////////////////////////////////////////////////////////////////////////////////
define('MYSQLSERVER','localhost');		#mysql server
define('MYSQLBASE','');					#mysql database
define('MYSQLNAME','');					#mysql user name
define('MYSQLPASSWORD','');				#mysql user password
define('announce_interval','1800');		# how often client will send requests
define('min_interval','300');			# how often client can force requests
define('default_peers','20');			# default # of peers to announce
define('max_peers','50');				# max # of peers to announce
?>