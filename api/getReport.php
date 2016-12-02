<?php
/*** COPYRIGHT NOTICE *********************************************************
 *
* Copyright 2009-2016 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
* Contributors : Julien PAPASIAN
*
* This file is part of ProjeQtOr.
*
* ProjeQtOr is free software: you can redistribute it and/or modify it under
* the terms of the GNU Affero General Public License as published by the Free
* Software Foundation, either version 3 of the License, or (at your option)
* any later version.
*
* ProjeQtOr is distributed in the hope that it will be useful, but WITHOUT ANY
* WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
* FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for
* more details.
*
* You should have received a copy of the GNU Affero General Public License along with
* ProjeQtOr. If not, see <http://www.gnu.org/licenses/>.
*
* You can get complete code of ProjeQtOr, other resource, help and information
* about contributors at http://www.projeqtor.org
*
*** DO NOT REMOVE THIS NOTICE ************************************************/

// For security reasons, this code can be disabled to avoid API access
// To disable API, just add $paramDisableAPI=true; in your parameters.php file
if (isset($paramDisableAPI) and $paramDisableAPI) {
	die; 
}

$batchMode = true;
require_once "../tool/projeqtor.php";
require_once "../external/phpAES/aes.class.php";
require_once "../external/phpAES/aesctr.class.php";
$batchMode = false;
// Look for user : can be found in 
// $_SERVER['PHP_AUTH_USER']
// $_SERVER['REMOTE_USER']
// $_SERVER['REDIRECT_REMOTE_USER']
// http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])['username']
$username = "";
if (isset($_SERVER['PHP_AUTH_USER'])) {
	$username = $_SERVER['PHP_AUTH_USER'];
} else if (isset($_SERVER['REMOTE_USER'])) {
	$username = $_SERVER['REMOTE_USER'];
} else if (isset($_SERVER['REDIRECT_REMOTE_USER'])) {
	$username = $_SERVER['REDIRECT_REMOTE_USER'];
} else if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
	$digest = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']);
	if ($digest and isset($digest['username'])) {
		$username = $digest['username'];
	}
}
if ($username) {
	$user = SqlElement::getSingleSqlElementFromCriteria('User', array('name' => $username));
} else {
	$user = new User(); 
	$cronnedScript = true;
}
if (!$user->id) {
	returnError('', "user '$username' unknown in database");
}
traceLog("API Report : mode = " . $_SERVER['REQUEST_METHOD'] . " user = $user->name, id = $user->id, profile = $user->idProfile");
setSessionUser($user);

if (isset($_REQUEST['id'])) {
	$id = intval($_REQUEST['id']);
	$obj = new Report();
	$where = "id = " . Sql::fmtId($id);

	// Add access restrictions
    $where .= ' and '.getAccesRestrictionClause('Report', null, true); // GOOD : access limit is applied !!!
	$list = $obj->getSqlElementsFromCriteria(null, null, $where);
	if(count($list) == 1) {
		$report = $list[0];
		$_REQUEST['page'] = '../report/' . $report->file;
		$_REQUEST['report'] = true;
		$_REQUEST['objectClass'] = $report->name;
		require_once '../view/print.php';
	} else {
		echo 'Too many rows';
	}
} else {
	echo 'No id specified';
}

function http_digest_parse($txt) {
	// protect against missing data
	$needed_parts = array('nonce' => 1, 'nc' => 1, 'cnonce' => 1, 'qop' => 1, 'username' => 1, 'uri' => 1, 'response' => 1);
	$data = array();
	$keys = implode('|', array_keys($needed_parts));

	preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

	foreach ($matches as $m) {
		$data[$m[1]] = $m[3] ? $m[3] : $m[4];
		unset($needed_parts[$m[1]]);
	}

	return $needed_parts ? false : $data;
}