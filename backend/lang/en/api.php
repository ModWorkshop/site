<?php

return [
    'webhook_event_mod_approval_new' => "The mod {mod.name} is waiting for approval.\n{mod_url}.",
    'webhook_event_mod_approval' => "The mod {mod.name} has been {status} by {moderator.name}\n{reason}\n{mod_url}.",
    'webhook_event_file_uploaded' => "New file {file.name} uploaded by {user.name}.\nDownload URL: {download_url}.\nMod URL: {mod_url}",
    'webhook_event_mod_suspended' => "The mod {mod.name} has been suspended by {moderator.name}.\nReason: {reason}\nMod Owner: https://modworkshop.net/user/{mod.user_id}.",
    'webhook_event_mod_deleted' => "The mod {mod.name} ({mod.id}) has been deleted.",
    'webhook_event_mod_published' => "The mod {mod.name} is now public for the first time in {location}\n{mod_url}",
    'webhook_event_mod_bumped' => "The mod {mod.name} has been updated\n{mod_url}",
    'webhook_event_report_new' => "New report has been made on {resource_type} by {reporter_user.name}.\n{resource_url}",
];
