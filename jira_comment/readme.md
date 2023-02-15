post_comment_to_jira(
    comment="Today's update: Progress is going well on this issue.", 
    issue_number="ISSUE-123", 
    jira_url="https://yourjiraurl.atlassian.net", 
    username="yourusername", 
    api_token="yourapitoken", 
    verbose=True
)

docker build -t jira-comment .


docker run --rm -e COMMENT="Today's update: Progress is going well on this issue." -e ISSUE_NUMBER="ISSUE-123" -e JIRA_URL="https://yourjiraurl.atlassian.net" -e USERNAME="yourusername" -e API_TOKEN="yourapitoken" jira-comment
