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

use Flarum\Extend;
use Flarum\Tags\Tag;
use Flarum\Testing\integration\RetrievesAuthorizedUsers;
use Flarum\Testing\integration\TestCase;
use Flarum\User\User;
use PHPUnit\Framework\Attributes\Test;

class TagTemplateTest extends TestCase
{
    use RetrievesAuthorizedUsers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension('flarum-tags', 'fof-discussion-templates');

        $this->prepareDatabase([
            User::class => [
                $this->normalUser(),
            ],
            Tag::class => [
                ['id' => 1, 'name' => 'General', 'slug' => 'general', 'position' => 0, 'parent_id' => null],
                ['id' => 2, 'name' => 'Support', 'slug' => 'support', 'position' => 1, 'parent_id' => null],
            ],
        ]);
    }

    #[Test]
    public function admin_can_set_tag_template()
    {
        $template = "# Bug Report Template\n\n## Expected Behavior\n\n## Actual Behavior";

        $response = $this->send(
            $this->request('PATCH', '/api/tags/1', [
                'authenticatedAs' => 1,
                'json'            => [
                    'data' => [
                        'type'       => 'tags',
                        'id'         => '1',
                        'attributes' => [
                            'template' => $template,
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals($template, $json['data']['attributes']['template']);

        // Verify it's persisted in database
        $tag = \Flarum\Tags\Tag::find(1);
        $this->assertNotNull($tag);
        $this->assertEquals($template, $tag->template);
    }

    #[Test]
    public function non_admin_cannot_set_tag_template()
    {
        $response = $this->send(
            $this->request('PATCH', '/api/tags/1', [
                'authenticatedAs' => 2,
                'json'            => [
                    'data' => [
                        'type'       => 'tags',
                        'id'         => '1',
                        'attributes' => [
                            'template' => 'Test template',
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(403, $response->getStatusCode());
    }

    #[Test]
    public function guest_cannot_set_tag_template()
    {
        $this->extend((new Extend\Csrf())->exemptRoute('tags.update'));

        $response = $this->send(
            $this->request('PATCH', '/api/tags/1', [
                'json' => [
                    'data' => [
                        'type'       => 'tags',
                        'id'         => '1',
                        'attributes' => [
                            'template' => 'Test template',
                        ],
                    ],
                ],
            ])
        );

        $this->assertContains($response->getStatusCode(), [403, 401]);
    }

    #[Test]
    public function tag_template_is_included_in_tag_list()
    {
        $template = 'Test template for general';
        $this->prepareDatabase([
            Tag::class => [
                ['id' => 1, 'template' => $template],
            ],
        ]);

        $response = $this->send(
            $this->request('GET', '/api/tags', [
                'authenticatedAs' => 1,
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);
        $generalTag = collect($json['data'])->firstWhere('id', '1');

        $this->assertNotNull($generalTag);
        $this->assertEquals($template, $generalTag['attributes']['template']);
    }

    #[Test]
    public function can_clear_tag_template()
    {
        // First set a template
        $this->prepareDatabase([
            Tag::class => [
                ['id' => 1, 'template' => 'Old template'],
            ],
        ]);

        // Now clear it
        $response = $this->send(
            $this->request('PATCH', '/api/tags/1', [
                'authenticatedAs' => 1,
                'json'            => [
                    'data' => [
                        'type'       => 'tags',
                        'id'         => '1',
                        'attributes' => [
                            'template' => '',
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody()->getContents(), true);
        $this->assertEquals('', $json['data']['attributes']['template']);
    }

    #[Test]
    public function tag_template_returns_404_for_nonexistent_tag()
    {
        $response = $this->send(
            $this->request('PATCH', '/api/tags/999', [
                'authenticatedAs' => 1,
                'json'            => [
                    'data' => [
                        'type'       => 'tags',
                        'id'         => '999',
                        'attributes' => [
                            'template' => 'Test',
                        ],
                    ],
                ],
            ])
        );

        $this->assertEquals(404, $response->getStatusCode());
    }
}
