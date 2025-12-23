<?php

use Illuminate\Database\Schema\Builder;

return [
    'up' => function (Builder $schema) {
        $db = $schema->getConnection();
        $prefix = $db->getTablePrefix();

        $db->table($prefix . 'settings')
            ->where('key', 'askvortsov-discussion-templates.no_tag_template')
            ->update(['key' => 'fof-discussion-templates.no_tag_template']);
    },
    'down' => function (Builder $schema) {
        //
    },
];
