<?php

/*
 * This file is part of fof/discussion-templates
 *
 * Copyright (c) Alexander Skvortsov, FriendsOfFlarum
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace FoF\DiscussionTemplates;

use Flarum\Api\Serializer\DiscussionSerializer;
use Flarum\Discussion\Discussion;
use Flarum\Discussion\Event\Saving;
use Flarum\Extend;
use Flarum\Tags\Api\Serializer\TagSerializer;
use Flarum\Tags\Tag;
use FoF\DiscussionTemplates\Access\DiscussionPolicy;
use FoF\DiscussionTemplates\Listener\SaveReplyTemplateToDatabase;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js'),

    new Extend\Locales(__DIR__.'/resources/locale'),

    (new Extend\Model(Discussion::class))
        ->cast('reply_template', 'string'),

    (new Extend\Model(Tag::class))
        ->cast('template', 'string'),

    (new Extend\Routes('api'))
        ->patch('/tags/{id}/template', 'tags.updateTemplate', Controller\UpdateTagTemplateController::class),

    (new Extend\ApiSerializer(TagSerializer::class))
        ->attribute('template', function (TagSerializer $serializer, Tag $model) {
            return $model->template;
        }),

    (new Extend\ApiSerializer(DiscussionSerializer::class))
        ->attribute('replyTemplate', function (DiscussionSerializer $serializer, Discussion $model) {
            return $model->reply_template;
        })
        ->attribute('canManageReplyTemplates', function (DiscussionSerializer $serializer, Discussion $model) {
            return $serializer->getActor()->can('manageReplyTemplates', $model);
        }),

    (new Extend\Policy())
        ->modelPolicy(Discussion::class, DiscussionPolicy::class),

    (new Extend\Event())
        ->listen(Saving::class, SaveReplyTemplateToDatabase::class),

    (new Extend\Settings())
        ->serializeToForum('fof-discussion-templates.no_tag_template', 'fof-discussion-templates.no_tag_template')
        ->serializeToForum('appendTemplateOnTagChange', 'appendTemplateOnTagChange', 'boolval'),
];
