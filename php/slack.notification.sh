#!/bin/bash

function post_to_slack () {
  # format message as a code block ```${msg}```
  SLACK_MESSAGE="\`\`\`$1\`\`\`"
  SLACK_URL=https://hooks.slack.com/services/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

  case "$2" in
    INFO)
      SLACK_ICON=':slack:'
      ;;
    WARNING)
      SLACK_ICON=':warning:'
      ;;
    ERROR)
      SLACK_ICON=':bangbang:'
      ;;
    *)
      SLACK_ICON=':slack:'
      ;;
  esac

  curl -X POST --data "payload={\"text\": \"${SLACK_ICON} Deploy to ${SLACK_MESSAGE}\ succeed"}" ${SLACK_URL}
}
post_to_slack $1 $2