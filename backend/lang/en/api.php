<?php

return [
    'webhook_event_mod_approval_new' => "The mod {mod.name}, which is owned by {mod.user.page_url}, is waiting for approval.\nMod URL: {mod.page_url}",
    'webhook_event_mod_approval' => "The mod {mod.name}, which is owned by {mod.user.page_url}, has been {status} by {initiator.name}\n{reason}\nMod URL: {mod.page_url}",
    'webhook_event_file_uploaded' => "New file {file.name} uploaded by {user.name}.\nDownload URL: {file.download_url}\nMod URL: {mod.page_url}",
    'webhook_event_mod_suspended' => "The mod {mod.name}, which is owned by {mod.user.page_url}, has been {status} by {initiator.name}\nReason: {reason}\nMod URL: {mod.page_url}",
    'webhook_event_mod_deleted' => "The mod {mod.name}, which is owned by {mod.user.page_url} ({mod.id}) has been deleted",
    'webhook_event_mod_published' => "The mod {mod.name} is now public for the first time in {mod.location}\nMod URL:{mod.page_url}",
    'webhook_event_mod_bumped' => "The mod {mod.name} has been updated\n{mod.page_url}",
    'webhook_event_report_new' => "New report has been made on {resource_type} by {initiator.name}\nReason: {reason}\n{resource.page_url}",
    'webhook_event_ticket_new' => "New ticket has been created: {thread.name} by {thread.user.name}\n{thread.page_url}",
    'webhook_event_ticket_reply' => "New ticket reply for ticket {thread.name} by {reply.user.name}\n{reply.page_url}",
];
