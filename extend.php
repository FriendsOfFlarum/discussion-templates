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

use Flarum\Api\Context;
use Flarum\Api\Resource;
use Flarum\Api\Schema;
use Flarum\Discussion\Discussion;
use Flarum\Extend;
use Flarum\Tags\Api\Resource\TagResource;
use Flarum\Tags\Tag;
use FoF\DiscussionTemplates\Access\DiscussionPolicy;

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

    // Add template field to TagResource
    (new Extend\ApiResource(TagResource::class))
        ->fields(fn () => [
            Schema\Str::make('template')
                ->writable(fn (Tag $_tag, Context $context) => $context->getActor()->isAdmin())
                ->nullable()
                ->get(fn (Tag $tag) => $tag->template),
        ]),

    // Add reply template fields to DiscussionResource
    (new Extend\ApiResource(Resource\DiscussionResource::class))
        ->fields(fn () => [
            Schema\Str::make('replyTemplate')
                ->writable(
                    fn (Discussion $discussion, Context $context) => $context->getActor()->can('manageReplyTemplates', $discussion)
                )
                ->nullable()
                ->get(fn (Discussion $discussion) => $discussion->reply_template)
                ->set(function (Discussion $discussion, ?string $value) {
                    $discussion->reply_template = $value;
                }),
            Schema\Boolean::make('canManageReplyTemplates')
                ->get(
                    fn (Discussion $discussion, Context $context) => $context->getActor()->can('manageReplyTemplates', $discussion)
                ),
        ]),

    (new Extend\Policy())
        ->modelPolicy(Discussion::class, DiscussionPolicy::class),

    (new Extend\Settings())
        ->serializeToForum('fof-discussion-templates.no_tag_template', 'fof-discussion-templates.no_tag_template')
        ->serializeToForum('appendTemplateOnTagChange', 'appendTemplateOnTagChange', 'boolval'),
];
