<?php
declare(strict_types=1);

return [
    'admin' => [
        'field' => [
            'common' => [
                'created_at' => 'Created At',
                'updated_at' => 'Updated At',
            ],
            'post' => [
                'content' => 'Content',
                'description' => 'Description',
                'id' => 'ID',
                'slug' => 'Slug',
                'status' => 'Status',
                'title' => 'Title',
            ],
        ],
    ],
    'entity' => [
        'post' => [
            'status' => [
                'draft' => 'Draft',
                'published' => 'Published',
            ],
        ],
    ],
];
