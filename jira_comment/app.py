import json
import requests
from flask import Flask, request, jsonify
from wrm import context_path

app = Flask(__name__)

def post_comment_to_jira(params, verbose=False):
    # Define your JIRA API URL
    jira_api_url = f"{params['jira_url']}/{context_path()}/rest/api/3/issue/{params['issue_number']}/comment"

    # Define your comment text
    comment_text = params['comment']

    # Create the payload for your comment
    payload = {
        "body": comment_text
    }

    # Convert payload to JSON
    payload_json = json.dumps(payload)

    # Send the API request to post the comment
    response = requests.post(
        jira_api_url,
        data=payload_json,
        auth=request.auth,
        headers={"Content-Type": "application/json"},
        verify=False  # skip SSL certificate verification
    )

    # Check the response status code
    if response.status_code == 201:
        if verbose:
            print("Comment added successfully!")
    else:
        if verbose:
            print(f"Error adding comment: {response.content}")
        response.raise_for_status()

@app.route('/post-comment-to-jira', methods=['POST'])
def handle_post_comment_to_jira():
    # Get the request parameters
    comment = request.form['comment']
    issue_number = request.form['issue_number']
    jira_url = request.form['jira_url']
    username = request.form['username']
    api_token = request.form['api_token']

    # Set the authentication credentials for the request
    request.auth = (username, api_token)

    # Call the post_comment_to_jira function
    post_comment_to_jira(
        params={
            'comment': comment,
            'issue_number': issue_number,
            'jira_url': jira_url
        },
        verbose=True
    )

    # Return a success response
    return jsonify({"success": True})

@app.route('/')
def index():
    comment = request.args.get('comment', '')
    issue_number = request.args.get('issue_number', '')
    jira_url = request.args.get('jira_url', '')

@app.route('/')
def index():
    return '''
        <html>
            <body>
                <h1>Post Comment to JIRA</h1>
                <form method="post" action="/post-comment-to-jira">
                    <label for="comment">Comment:</label><br>
                    <input type="text" id="comment" name="comment"><br>
                    <label for="issue_number">Issue Number:</label><br>
                    <input type="text" id="issue_number" name="issue_number"><br>
                    <label for="jira_url">JIRA URL:</label><br>
                    <input type="text" id="jira_url" name="jira_url"><br>
                    <label for="username">Username:</label><br>
                    <input type="text" id="username" name="username"><br>
                    <label for="api_token">API Token:</label><br>
                    <input type="text" id="api_token" name="api_token"><br><br>
                    <input type="submit" value="Submit">
                </form>
            </body>
        </html>
    '''

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=int(os.environ.get('PORT', 5000)))