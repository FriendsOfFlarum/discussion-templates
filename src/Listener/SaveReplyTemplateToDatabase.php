<?php

/*
 * This file is part of fof/discussion-templates
 *
 * Copyright (c) Alexander Skvortsov, FriendsOfFlarum
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace FoF\DiscussionTemplates\Listener;

use Flarum\Discussion\Event\Saving;
use Illuminate\Support\Arr;

class SaveReplyTemplateToDatabase
{
    public function handle(Saving $event)
    {
        $discussion = $event->discussion;
        $data = $event->data;
        $actor = $event->actor;

        $attributes = Arr::get($data, 'attributes', []);

        if (isset($attributes['replyTemplate'])) {
            $actor->assertCan('manageReplyTemplates', $discussion);

            $discussion->reply_template = $attributes['replyTemplate'];
        }
    }
}
