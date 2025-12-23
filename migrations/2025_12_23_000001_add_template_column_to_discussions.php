<?php

/*
 * This file is part of fof/discussion-templates
 *
 * Copyright (c) Alexander Skvortsov, FriendsOfFlarum
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasColumn('discussions', 'template')) {
            $schema->table('discussions', function (Blueprint $table) {
                $table->text('template')->default('');
            });
        }
    },
    'down' => function (Builder $schema) {
        $schema->table('discussions', function (Blueprint $table) {
            $table->dropColumn('template');
        });
    },
];
