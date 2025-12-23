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
use Flarum\Extend;

class DiscussionReplyTemplateTest extends TestCase
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
            ],
            'discussions' => [
                ['id' => 1, 'title' => 'Test Discussion', 'user_id' => 2, 'created_at' => Carbon::now(), 'comment_count' => 1],
                ['id' => 2, 'title' => 'Another Discussion', 'user_id' => 3, 'created_at' => Carbon::now(), 'comment_count' => 1],
            ],
            'posts' => [
                ['id' => 1, 'discussion_id' => 1, 'user_id' => 2, 'type' => 'comment', 'content' => '<t><p>First post</p></t>', 'created_at' => Carbon::now(), 'number' => 1],
                ['id' => 2, 'discussion_id' => 2, 'user_id' => 3, 'type' => 'comment', 'content' => '<t><p>First post</p></t>', 'created_at' => Carbon::now(), 'number' => 1],
            ],
            'group_user' => [
                ['user_id' => 2, 'group_id' => 4], // User 2 in Members group
                ['user_id' => 3, 'group_id' => 3], // User 3 in Moderators group
            ],
        ]);
    }

    /**
     * @test
     */
    public function discussion_owner_can_set_reply_template()
    {
        // Grant permission to manage own discussion reply templates
        $this->prepareDatabase([
            'group_permission' => [
                ['group_id' => 4, 'permission' => 'discussion.manageOwnDiscussionReplyTemplates'],
            ],
        ]);

        $template = "Please provide:\n- Version\n- Steps to reproduce";

        $response = $this->send(
            $this->request('PATCH', '/api/discussions/1', [
                'authenticatedAs' => 2,
                'json' => [
                    'data' => [
                        'attributes' => [
                            'replyTemplate' => $template,
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals($template, $json['data']['attributes']['replyTemplate']);
    }

    /**
     * @test
     */
    public function discussion_owner_cannot_set_reply_template_without_permission()
    {
        // Remove the permission
        $this->database()->table('group_permission')
            ->where('permission', 'discussion.manageOwnDiscussionReplyTemplates')
            ->delete();

        $response = $this->send(
            $this->request('PATCH', '/api/discussions/1', [
                'authenticatedAs' => 2,
                'json' => [
                    'data' => [
                        'attributes' => [
                            'replyTemplate' => 'Test template',
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
    public function moderator_can_set_any_discussion_reply_template()
    {
        // Grant permission to moderators (group 3)
        $this->prepareDatabase([
            'group_permission' => [
                ['group_id' => 3, 'permission' => 'discussion.manageAllReplyTemplates'],
            ],
        ]);

        $template = "Moderator template";

        $response = $this->send(
            $this->request('PATCH', '/api/discussions/1', [
                'authenticatedAs' => 3,
                'json' => [
                    'data' => [
                        'attributes' => [
                            'replyTemplate' => $template,
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals($template, $json['data']['attributes']['replyTemplate']);
    }

    /**
     * @test
     */
    public function non_owner_non_moderator_cannot_set_reply_template()
    {
        $response = $this->send(
            $this->request('PATCH', '/api/discussions/2', [
                'authenticatedAs' => 2,
                'json' => [
                    'data' => [
                        'attributes' => [
                            'replyTemplate' => 'Unauthorized template',
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
    public function guest_cannot_set_reply_template()
    {
        $this->extend((new Extend\Csrf())->exemptRoute('discussions.update'));
        
        $response = $this->send(
            $this->request('PATCH', '/api/discussions/1', [
                'json' => [
                    'data' => [
                        'attributes' => [
                            'replyTemplate' => 'Guest template',
                        ],
                    ],
                ],
            ])
        );

        $this->assertContains($response->getStatusCode(), [403]);
    }

    /**
     * @test
     */
    public function reply_template_is_included_in_discussion_response()
    {
        $template = 'Test reply template';
        $this->prepareDatabase([
            'discussions' => [
                ['id' => 1, 'reply_template' => $template],
            ],
        ]);

        $response = $this->send(
            $this->request('GET', '/api/discussions/1', [
                'authenticatedAs' => 1,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals($template, $json['data']['attributes']['replyTemplate']);
    }

    /**
     * @test
     */
    public function can_clear_reply_template()
    {
        // Grant permission and set initial template
        $this->prepareDatabase([
            'group_permission' => [
                ['group_id' => 4, 'permission' => 'discussion.manageOwnDiscussionReplyTemplates'],
            ],
            'discussions' => [
                ['id' => 1, 'reply_template' => 'Old template'],
            ],
        ]);

        // Now clear it
        $response = $this->send(
            $this->request('PATCH', '/api/discussions/1', [
                'authenticatedAs' => 2,
                'json' => [
                    'data' => [
                        'attributes' => [
                            'replyTemplate' => '',
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('', $json['data']['attributes']['replyTemplate']);
    }

    /**
     * @test
     */
    public function can_manage_reply_templates_attribute_is_included()
    {
        // Grant permission to manage own discussion reply templates
        $this->prepareDatabase([
            'group_permission' => [
                ['group_id' => 4, 'permission' => 'discussion.manageOwnDiscussionReplyTemplates'],
            ],
        ]);

        $response = $this->send(
            $this->request('GET', '/api/discussions/1', [
                'authenticatedAs' => 2,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('canManageReplyTemplates', $json['data']['attributes']);
        $this->assertTrue($json['data']['attributes']['canManageReplyTemplates']);
    }

    /**
     * @test
     */
    public function can_manage_reply_templates_is_false_for_unauthorized_user()
    {
        $response = $this->send(
            $this->request('GET', '/api/discussions/2', [
                'authenticatedAs' => 2,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);
        $this->assertArrayHasKey('canManageReplyTemplates', $json['data']['attributes']);
        $this->assertFalse($json['data']['attributes']['canManageReplyTemplates']);
    }
}
