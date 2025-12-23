<?php

/*
 * This file is part of fof/discussion-templates
 *
 * Copyright (c) Alexander Skvortsov, FriendsOfFlarum
 *
 * For detailed copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */

namespace FoF\DiscussionTemplates\Tests\integration\api;

use Carbon\Carbon;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;

class PermissionsTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('flarum-tags', 'fof-discussion-templates');

        $this->prepareDatabase([
            'users' => [
                $this->normalUser(),
                ['id' => 3, 'username' => 'moderator', 'email' => 'mod@machine.local', 'is_email_confirmed' => true],
                ['id' => 4, 'username' => 'user3', 'email' => 'user3@machine.local', 'is_email_confirmed' => true],
            ],
            'discussions' => [
                ['id' => 1, 'title' => 'User 2 Discussion', 'user_id' => 2, 'created_at' => Carbon::now(), 'comment_count' => 1],
                ['id' => 2, 'title' => 'User 3 Discussion', 'user_id' => 3, 'created_at' => Carbon::now(), 'comment_count' => 1],
                ['id' => 3, 'title' => 'User 4 Discussion', 'user_id' => 4, 'created_at' => Carbon::now(), 'comment_count' => 1],
            ],
            'posts' => [
                ['id' => 1, 'discussion_id' => 1, 'user_id' => 2, 'type' => 'comment', 'content' => '<t><p>Post 1</p></t>', 'created_at' => Carbon::now(), 'number' => 1],
                ['id' => 2, 'discussion_id' => 2, 'user_id' => 3, 'type' => 'comment', 'content' => '<t><p>Post 2</p></t>', 'created_at' => Carbon::now(), 'number' => 1],
                ['id' => 3, 'discussion_id' => 3, 'user_id' => 4, 'type' => 'comment', 'content' => '<t><p>Post 3</p></t>', 'created_at' => Carbon::now(), 'number' => 1],
            ],
            'group_user' => [
                ['user_id' => 2, 'group_id' => 4], // Members
                ['user_id' => 3, 'group_id' => 3], // Moderators
                ['user_id' => 4, 'group_id' => 4], // Members
            ],
        ]);
    }

    /**
     * @test
     */
    public function user_with_own_permission_can_manage_own_discussion()
    {
        // Grant permission to members to manage their own discussions
        $this->prepareDatabase([
            'group_permission' => [
                ['group_id' => 4, 'permission' => 'discussion.manageOwnDiscussionReplyTemplates'],
            ],
        ]);

        $response = $this->send(
            $this->request('PATCH', '/api/discussions/1', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'attributes' => [
                            'replyTemplate' => 'Own template',
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_with_own_permission_cannot_manage_others_discussion()
    {
        $this->prepareDatabase([
            'group_permission' => [
                ['group_id' => 4, 'permission' => 'discussion.manageOwnDiscussionReplyTemplates'],
            ],
        ]);

        $response = $this->send(
            $this->request('PATCH', '/api/discussions/2', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'attributes' => [
                            'replyTemplate' => 'Unauthorized',
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_with_all_permission_can_manage_any_discussion()
    {
        // Grant "all" permission to members group
        $this->prepareDatabase([
            'group_permission' => [
                ['group_id' => 4, 'permission' => 'discussion.manageAllReplyTemplates'],
            ],
        ]);

        // User 2 managing User 3's discussion
        $response = $this->send(
            $this->request('PATCH', '/api/discussions/2', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'attributes' => [
                            'replyTemplate' => 'All permission template',
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function user_without_permission_cannot_manage_any_discussion()
    {
        // No permissions granted - test will use empty permission set

        $response = $this->send(
            $this->request('PATCH', '/api/discussions/1', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'attributes' => [
                            'replyTemplate' => 'No permission',
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function moderator_group_has_all_permission_by_default()
    {
        // Make user 2 a moderator and grant moderator permission
        $this->prepareDatabase([
            'group_permission' => [
                ['group_id' => 3, 'permission' => 'discussion.manageAllReplyTemplates'],
            ],
            'group_user' => [
                ['user_id' => 2, 'group_id' => 3],
            ],
        ]);

        // User 2 (now a moderator) managing User 4's discussion
        $response = $this->send(
            $this->request('PATCH', '/api/discussions/3', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'attributes' => [
                            'replyTemplate' => 'Moderator template',
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function all_permission_takes_precedence_over_own_permission()
    {
        // Grant both permissions to members group
        $this->prepareDatabase([
            'group_permission' => [
                ['group_id' => 4, 'permission' => 'discussion.manageOwnDiscussionReplyTemplates'],
                ['group_id' => 4, 'permission' => 'discussion.manageAllReplyTemplates'],
            ],
        ]);

        // User with both permissions can manage others' discussions
        $response = $this->send(
            $this->request('PATCH', '/api/discussions/3', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'attributes' => [
                            'replyTemplate' => 'Both permissions',
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function can_manage_reply_templates_reflects_permission_state()
    {
        $this->prepareDatabase([
            'group_permission' => [
                ['group_id' => 4, 'permission' => 'discussion.manageOwnDiscussionReplyTemplates'],
            ],
        ]);

        // Check own discussion
        $response = $this->send(
            $this->request('GET', '/api/discussions/1', [
                'authenticatedAs' => 2,
            ])
        );

        $json = json_decode($response->getBody()->getContents(), true);
        $this->assertTrue($json['data']['attributes']['canManageReplyTemplates']);

        // Check others' discussion
        $response = $this->send(
            $this->request('GET', '/api/discussions/2', [
                'authenticatedAs' => 2,
            ])
        );

        $json = json_decode($response->getBody()->getContents(), true);
        $this->assertFalse($json['data']['attributes']['canManageReplyTemplates']);
    }
}
