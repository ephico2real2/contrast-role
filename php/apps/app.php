<?php
require_once 'vendor/autoload.php';

use Unirest\Request;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jiraUrl = $_POST['jira-url'];
    $jiraToken = $_POST['jira-token'];
    $issueNumber = $_POST['issue-number'];
    $issueSummary = '';
    $comment = $_POST['comment'];
    $lookupIssue = isset($_POST['lookup-issue']) && $_POST['lookup-issue'] === 'true';

    // If lookupIssue is true, retrieve issue details
    if ($lookupIssue) {
        $issueUrl = $jiraUrl . '/rest/api/2/issue/' . $issueNumber;
        $issueHeaders = array('Authorization' => 'Bearer ' . $jiraToken);
        $issueResponse = Request::get($issueUrl, $issueHeaders);
        
        if ($issueResponse->code === 200) {
            $issueJson = json_decode($issueResponse->raw_body);
            $issueSummary = $issueJson->fields->summary;
        } else {
            error_log('Error retrieving issue details from JIRA.');
            echo 'Error retrieving issue details from JIRA.';
            return;
        }
    }

    // Send comment to JIRA
    $commentUrl = $jiraUrl . '/rest/api/2/issue/' . $issueNumber . '/comment';
    $commentHeaders = array('Authorization' => 'Bearer ' . $jiraToken, 'Content-Type' => 'application/json');
    $commentData = array('body' => $comment);
    $commentResponse = Request::post($commentUrl, $commentHeaders, json_encode($commentData));

    // Log JIRA response to console
    error_log('JIRA response: ' . print_r($commentResponse, true));

    // Check the comment response
    if ($commentResponse->code === 201) {
        echo 'Comment posted to JIRA successfully.';
    } else {
        error_log('Error posting comment to JIRA.');
        echo 'Error posting comment to JIRA.';
    }
}
?>
