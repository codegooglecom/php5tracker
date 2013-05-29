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

// 20-bytes - info_hash
if (!isset($_GET['info_hash']) || !is_string($_GET['info_hash']) || strlen($_GET['info_hash']) != 20) tracker_error('info-hash is invalid');
$info_hash = reverse_escape($_GET['info_hash']);

// client port
if (!isset($_GET['port']) || !is_numeric($_GET['port']) || ($_GET['port'] <1) || ($_GET['port'] >65535)) tracker_error('client port is invalid');
$port = $_GET['port'];

// client ip
if (!isset($_SERVER['REMOTE_ADDR'])) tracker_error('could not locate clients ip');
$ip = trim($_SERVER['REMOTE_ADDR'],'::ffff:');

// 6-byte compacted peer info (ip + port)
$compact = reverse_escape(pack('Nn', ip2long($ip), $port));

// number of bytes left for the peer to download
if (!isset($_GET['left']) || !is_numeric($_GET['left'])) tracker_error('client data left field is invalid');

// $status = 0 - seed, $status = 1 - leach
if ($_GET['left'] ==0) $status = 0; else $status = 1;

// number of peers that the client has requested
if (!isset($_GET['numwant'])) $num_peers = default_peers; else $num_peers = MIN($_GET['numwant']+0, max_peers); 

// connect to database
$db = mysqli_connect(MYSQLSERVER,MYSQLNAME,MYSQLPASSWORD,MYSQLBASE);
if (mysqli_connect_errno()) tracker_error('MySQL connection failed: ' . mysqli_connect_error());

// time / expire
$timestamp = time();
$expired = $timestamp - (announce_interval+min_interval);

// delete current peer from peers table
if (mysqli_query($db, "DELETE FROM peers WHERE info_hash='$info_hash' AND compact='$compact'") === FALSE) tracker_error('cannot process MySQL query');

// delete outdated peers, by random announcing client - roughtly 1 of 6 peers runs the clean-up
if (mt_rand(1, 6) == 6) 
{
if (mysqli_query($db, "DELETE FROM peers WHERE updated < '$expired'") === FALSE) tracker_error('cannot process MySQL query');
}

// checked if event != stopped and insert current peer into 'peers' table
if (isset($_GET['event']) && ($_GET['event'] == 'stopped')) $stopped=1; else {
$stopped=0;
if (mysqli_query($db, "INSERT IGNORE INTO peers (info_hash, compact, state, updated) VALUES ('$info_hash', '$compact', '$status', '$timestamp')")  === FALSE) tracker_error('cannot process MySQL query');
}

// scrape info_hash (count seeders and leechers for specific info_hash) and create peers list
$q_peers = mysqli_query($db, "SELECT compact, state FROM peers WHERE info_hash='$info_hash' ORDER BY RAND()");

$totals=0;
$seeders=0;
$leechers=0;
$peers = '';

while($peer = mysqli_fetch_row($q_peers))
{
$totals += 1;
if (($totals <= $num_peers) && ($stopped == 0)) $peers .= $peer[0];
$leechers += $peer[1];
}
$seeders = $totals - $leechers;

// free-up the SQL result
mysqli_free_result($q_peers);

// create response
$response = 'd8:completei' . $seeders . 'e10:downloadedi0e10:incompletei' . $leechers; 
$response .= 'e8:intervali' . announce_interval . 'e12:min intervali' . min_interval;
$response .= 'e5:peers' . strlen($peers) . ':' . $peers . 'e';

// send response
echo $response;

// OPTIMIZE TABLE peers by random announcing client - roughtly each 64th runs the optimization
if (mt_rand(1, 64) == 64) 
{
if (mysqli_query($db, "OPTIMIZE TABLE peers") === FALSE) tracker_error('cannot OPTIMIZE TABLE');
}

// close database and exit
mysqli_close($db);
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