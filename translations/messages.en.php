<?php
declare(strict_types=1);

return [
    'admin' => [
        'field' => [
            'post' => [
                'content' => 'Content',
                'description' => 'Description',
                'id' => 'ID',
                'slug' => 'Slug',
                'status' => 'Status',
                'title' => 'Title',
                'createdAt' => 'Created At',
                'updatedAt' => 'Updated At',
            ],
        ],
        'form' => [
            'errors' => [
                'post_image' => [
                    'unsupported_mime_type' => 'Unsupported mime type. Supported mime types: [ mimeTypes ]',
                ],
            ]
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
