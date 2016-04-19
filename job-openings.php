<?php

include_once('includes/config.inc.php');
include_once($cfg['includes'] . 'text-functions.inc.php');

if (!empty($_GET['view'])) {

	// Get job details
	$sql = "SELECT jo.*, la.university_name  FROM `job_openings` jo LEFT JOIN `la_programs` la ON (la.la_program_id = jo.la_program_id) WHERE `job_opening_id` = '{$_GET['view']}'";
	$db->query($sql);
	$job = $db->fetchRow();
	$meta['title'] = $job['position'];
	$job['date_posted'] = date ("j M Y", strtotime($job['date_posted']));
	$job['expire_date'] = date ("j M Y", strtotime($job['expire_date']));
	
	// get keywords associated with this job
	$sql = "SELECT k.* FROM `keywords` AS k LEFT JOIN `keywords_job_openings_map` AS km on (k.keyword_id = km.keyword_id) WHERE km.job_opening_id = '{$_GET['view']}'";
	$db->query($sql);
	$keywordNum = $db->numRows();
	
	$keywordsArr = array();
	while ($row = $db->nextRow()) {
		$keywordsArr[] = $row['keyword'];
	}
	
	// Setup up the template
	$tpl->assign('meta', $meta);
	$tpl->assign('job', $job);
	$tpl->assign('keywordNum', $keywordNum);
	$tpl->assign('keywordsArr', $keywordsArr);
	$tpl->assign('jobOpeningArr', $jobOpeningArr);
	$tpl->display('header.php');
	$tpl->display('job-openings-view.tpl.php');
	//tpl->display('footer.php');
	
} else {

	$sql = "SELECT * FROM job_openings WHERE expire_date > NOW() ORDER BY date_posted DESC";

	$db->query($sql);
	
	$jobOpeningArr = array();
	while ($row = $db->nextRow()) {
		$jobOpeningArr[] = array(
			'pYear'        	 => date("Y", strtotime($row['date_posted'])),
			'position_link'  => $cfg['siteurl'].'/'.basename($_SERVER['PHP_SELF']).'?view='.$row['job_opening_id'],
			'position_title' => htmlentities($row['position']),
			'position'       => str_replace('&amp;#8230;', '&#8230;', htmlentities(truncateStr($row['position'], 90)))
		);
	}
	
	$meta['title'] = 'Job Openings';
	$tpl->assign('meta', $meta);
	$tpl->assign('jobOpeningArr', $jobOpeningArr);
	$tpl->display('header.php');
	$tpl->display('job-openings.tpl.php');
	 $tpl->display('footer.php'); 

}
?>