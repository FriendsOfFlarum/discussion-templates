# FoF Discussion Templates

![License](https://img.shields.io/badge/license-MIT-blue.svg) [![Latest Stable Version](https://img.shields.io/packagist/v/FriendsOfFlarum/discussion-templates.svg)](https://packagist.org/packages/FriendsOfFlarum/discussion-templates)

A [Flarum](http://flarum.org) extension that allows you to create customizable templates for new discussions. Templates can be assigned per tag or set as a default for discussions without tags. Additionally, discussion owners and moderators can set reply templates to guide responses.

> **Note:** This extension is a direct replacement for the abandoned [`askvortsov/flarum-discussion-templates`](https://github.com/askvortsov1/flarum-discussion-templates). Upgrading from the old version is seamless - simply install this new version and your existing templates and settings will be automatically migrated!

## Features

- **Tag-Based Templates**: Create specific templates for each tag to guide users on what information to include
- **Default Template**: Set a fallback template for discussions created without any tags
- **Reply Templates**: Discussion owners can set templates that appear when users reply to their discussions
- **Permission System**: Granular control over who can manage templates
  - `discussion.manageOwnDiscussionReplyTemplates` - Allow users to manage reply templates on their own discussions
  - `discussion.manageAllReplyTemplates` - Allow users (e.g., moderators) to manage reply templates on any discussion
- **Auto-Insert**: Templates automatically populate the composer when creating new discussions or can append when tags change

## Installation

Install with composer:

```sh
composer require fof/discussion-templates
php flarum migrate
php flarum cache:clear
```

## Setup

### Tag Templates

1. Navigate to the Tags page in your admin panel
2. Click on a tag you want to add a template to
3. In the modal, you'll see a "Discussion Template" field
4. Enter your template using markdown or BBCode formatting
5. Save the tag

### Default Template (No Tags)

1. Go to the Extensions page in your admin panel
2. Find "FoF Discussion Templates" and click settings
3. Enter your default template in the "Template for discussions with no tag" field
4. You can also enable "Append template when tags change" to update the composer when users modify tags

### Reply Templates

**For Discussion Owners:**
1. Navigate to one of your discussions
2. Click the three-dot menu and select "Set Reply Template"
3. Enter the template you want users to see when replying
4. Save

**For Moderators:**
- Moderators with the appropriate permission can set reply templates on any discussion using the same method

## Permissions

Configure permissions in your admin panel under the Permissions page:

- **Manage own discussion reply templates**: Allows users to set reply templates on discussions they created
- **Manage all discussion reply templates**: Allows users (typically moderators) to set reply templates on any discussion

## Usage

### Creating a Discussion
When a user creates a new discussion:
- If a tag is selected and has a template, that template will be inserted into the composer
- If no tags are selected and a default template exists, it will be inserted
- If multiple tags are selected with templates, the primary tag's template takes precedence

### Replying to a Discussion
When a discussion has a reply template set:
- The template will appear in the composer when users click "Reply"
- Users can modify or remove the template as needed before posting

## Notes

- Templates support markdown and BBCode formatting
- The composer preview is not available in the admin area. To preview formatting, create a test discussion in the forum and copy the formatted content
- Templates are automatically inserted when the composer is opened, but users can freely edit or remove them

## Updating

```sh
composer update fof/discussion-templates
php flarum migrate
php flarum cache:clear
```

## Links

- [Packagist](https://packagist.org/packages/FriendsOfFlarum/discussion-templates)
- [Github](https://github.com/FriendsOfFlarum/discussion-templates)
- [Discuss](https://discuss.flarum.org/d/23950-discussion-templates-per-tag)
