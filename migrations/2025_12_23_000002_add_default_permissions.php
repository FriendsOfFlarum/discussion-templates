<?php

use Flarum\Database\Migration;
use Flarum\Group\Group;

return Migration::addPermissions([
    'discussion.manageOwnDiscussionReplyTemplates' => Group::MEMBER_ID,
    'discussion.manageAllReplyTemplates'           => Group::MODERATOR_ID,
]);
