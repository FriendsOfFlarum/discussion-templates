<?php

/*
 * This file is part of fof/discussion-templates
 *
 * Copyright (c) Alexander Skvortsov, FriendsOfFlarum
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace FoF\DiscussionTemplates\Access;

use Flarum\Discussion\Discussion;
use Flarum\User\Access\AbstractPolicy;
use Flarum\User\User;

class DiscussionPolicy extends AbstractPolicy
{
    /**
     * @param User       $actor
     * @param Discussion $discussion
     *
     * @return bool
     */
    public function manageReplyTemplates(User $actor, Discussion $discussion)
    {
        return $actor->can('manageAllReplyTemplates', $discussion) || $actor->id === $discussion->user_id && $actor->can('manageOwnDiscussionReplyTemplates', $discussion);
    }
}
