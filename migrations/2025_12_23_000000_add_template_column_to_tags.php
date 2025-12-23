<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        if (!$schema->hasColumn('tags', 'template')) {
            $schema->table('tags', function (Blueprint $table) {
                $table->text('template')->default('');
            });
        }
    },
    'down' => function (Builder $schema) {
        $schema->table('tags', function (Blueprint $table) {
            $table->dropColumn('template');
        });
    },
];
