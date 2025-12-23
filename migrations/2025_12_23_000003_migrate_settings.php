<?php

/*
 * This file is part of fof/discussion-templates
 *
 * Copyright (c) Alexander Skvortsov, FriendsOfFlarum
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $db = $schema->getConnection();

        $db->table('settings')
            ->where('key', 'askvortsov-discussion-templates.no_tag_template')
            ->update(['key' => 'fof-discussion-templates.no_tag_template']);
    },
    'down' => function (Builder $schema) {
        //
    },
];
