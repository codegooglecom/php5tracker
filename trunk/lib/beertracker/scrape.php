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
if (!isset($_GET['info_hash']) || !is_string($_GET['info_hash']) || strlen($_GET['info_hash']) != 20) tracker_error('fullscrape not supported');
$info_hash = reverse_escape($_GET['info_hash']);

// connect to database
$db = mysqli_connect(MYSQLSERVER,MYSQLNAME,MYSQLPASSWORD,MYSQLBASE);
if (mysqli_connect_errno()) tracker_error('MySQL connection failed: ' . mysqli_connect_error());

// scrape info_hash and count seeders and leechers
$scrape = mysqli_query($db, "SELECT state FROM peers WHERE info_hash='$info_hash'");
$totals=0;
$seeders=0;
$leechers=0;

while($peer = mysqli_fetch_row($scrape))
{
$leechers += $peer[0];
$totals += 1;
}
$seeders = $totals - $leechers;

// build response
$response .= 'd5:filesd20:' . $info_hash . 'd8:completei' . $seeders . 'e10:downloadedi0e10:incompletei' . $leechers . 'eeee';

// send response
echo $response;

// cleanup
mysqli_free_result($scrape);

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