<?php

require_once __DIR__ . '/vendor/autoload.php';

use Unirest\Request;
use Unirest\Request\Body;

// JiraParams holds the JIRA API parameters
class JiraParams
{
    public $issue_number;
    public $comment;
}

// Credentials holds the JIRA API credentials
class Credentials
{
    public $username;
    public $api_token;
}

// getJiraAPIURL constructs the JIRA API URL
function getJiraAPIURL($jiraURL, $issueNumber)
{
    return $jiraURL . '/rest/api/3/issue/' . $issueNumber . '/comment';
}

// postCommentToJira posts a comment to a JIRA issue
function postCommentToJira($jiraURL, $issueNumber, $comment, $username, $apiToken)
{
    // Construct the JIRA API URL
    $jiraAPIURL = getJiraAPIURL($jiraURL, $issueNumber);

    // Create the comment payload
    $payload = [
        'body' => $comment,
    ];

    // Post the comment to JIRA
    $response = Request::post($jiraAPIURL, [
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic ' . base64_encode($username . ':' . $apiToken),
    ], Body::json($payload), null, true, [
        'ssl_verify_peer' => false, // Skip SSL verification
    ]);

    if ($response->code != 201) {
        return $response->raw_body;
    }

    return null;
}

// postCommentHandler handles the POST request to post a comment to a JIRA issue
function postCommentHandler()
{
    // Parse the request POST data
    $jiraParams = json_decode(file_get_contents('php://input'), true);

    // Load the environment variables
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // Get the JIRA URL and API credentials from the POST data or the environment variables
    $jiraURL = $jiraParams['jira_url'] ?? $_ENV['JIRA_URL'];
    $username = $jiraParams['username'] ?? $_ENV['JIRA_USERNAME'];
    $apiToken = $jiraParams['api_token'] ?? $_ENV['JIRA_API_TOKEN'];

    // Post the comment to JIRA
    $err = postCommentToJira($jiraURL, $jiraParams['issue_number'], $jiraParams['comment'], $username, $apiToken);
    if ($err) {
        http_response_code(500);
        echo json_encode(array('error' => $err));
        return;
    }

    // Return a success response
    http_response_code(200);
    echo json_encode(array('success' => true));
}

// homeHandler handles the GET request to the home page
function homeHandler()
{
    header('Content-Type: text/html; charset=utf-8');
?>
    <html>

    <head>
        <title>Post Comment to JIRA</title>
    </head>

    <body>
        <h1>Post Comment to JIRA</h1>
        <form action="/post-comment-to-jira" method="post">
            <label for="issue_number">Issue Number:</label>
            <input type="text" name="issue_number"><br>
            <label for="comment">Comment:</
            <textarea name="comment"></textarea><br>
            <input type="hidden" name="jira_url" value="<?php echo $_ENV['JIRA_URL']; ?>">
            <input type="hidden" name="username" value="<?php echo $_ENV['JIRA_USERNAME']; ?>">
            <input type="hidden" name="api_token" value="<?php echo $_ENV['JIRA_API_TOKEN']; ?>">
            <input type="submit" value="Submit">
        </form>
    </body>

    </html>
<?php
}

// Define the routes
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/') {
    homeHandler();
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/post-comment-to-jira') {
    postCommentHandler();
} else {
    header('HTTP/1.1 404 Not Found');
    echo 'Page not found';
}