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
require_once 'config.php';
///////////////////////////////////////////////////////////////////////////////////////////////
error_reporting(E_ALL & ~E_WARNING);
set_time_limit(0);
ignore_user_abort(true);

// connect to database
$db = mysqli_connect(MYSQLSERVER,MYSQLNAME,MYSQLPASSWORD,MYSQLBASE);
if (mysqli_connect_errno()) tracker_error('MySQL connection failed: ' . mysqli_connect_error());

// time / expire
$timestamp = time();
$expired = $timestamp - (announce_interval+min_interval);
if (mysqli_query($db, "DELETE FROM peers WHERE updated < '$expired'") === FALSE) tracker_error('cannot process MySQL query');
if (mysqli_query($db, "OPTIMIZE TABLE peers") === FALSE) tracker_error('cannot OPTIMIZE TABLE');

// count seeders and leechers
$fullscrape = mysqli_query($db, "SELECT info_hash, state FROM peers ORDER BY info_hash");
$torrents=0;
$totals=0;
$seeders=0;
$leechers=0;
$info_hash='';
while($stats = mysqli_fetch_row($fullscrape))
{
$leechers += $stats[1];
$totals += 1;
if ($info_hash != $stats[0])
	{
	$info_hash = $stats[0];
	$torrents +=1;
	}
}
$seeders = $totals - $leechers;

// cleanup
mysqli_free_result($fullscrape);

// close database
mysqli_close($db);

echo '<!doctype html><html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8">' .
				     '<title>BeerTracker statistics:</title>' .
					 '<body><pre>' . number_format($totals) . 
				     ' peers (' . number_format($seeders) . ' seeders + ' . number_format($leechers) .
				     ' leechers) in ' . number_format($torrents) . ' torrents</pre></body></html>';
					 
exit;

// fatal error, stop execution
function tracker_error($error) 
{
	exit('d14:failure reason' . strlen($error) . ":{$error}e");
}

function reverse_escape($str)
{
	$search=array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
	$replace=array("\\","\0","\n","\r","\x1a","'",'"');
	return str_replace($search,$replace,$str);
}
?>