import app from 'flarum/forum/app';
import { extend, override } from 'flarum/common/extend';
import IndexPage from 'flarum/forum/components/IndexPage';
import ComposerState from 'flarum/forum/states/ComposerState';
import type Tag from 'flarum/tags/common/models/Tag';

function insertTemplate(contentOverwrite = false) {
  if (!app.composer.fields) return;

  const composerFields = app.composer.fields as any;
  if (!composerFields.tags) return;

  const composerBody = app.composer.body as any;
  const original = composerBody.attrs?.originalContent || '';
  const content = app.composer.fields.content().trim();
  if (content !== original && !app.forum.attribute('appendTemplateOnTagChange')) return;

  const templateCandidates: Record<string, string> = {};

  composerFields.tags.forEach(function (tag: Tag) {
    if (tag.position() === null || !tag.template()) return;

    templateCandidates[tag.id()!] = tag.template();
  });

  const ids = Object.keys(templateCandidates);

  if (ids.length === 2) {
    const first = app.store.getById<Tag>('tags', ids[0]);
    const second = app.store.getById<Tag>('tags', ids[1]);
    if (first && second) {
      if (first.parent() === second) {
        delete templateCandidates[ids[1]];
      }
      if (second.parent() === first) {
        delete templateCandidates[ids[0]];
      }
    }
  }

  if (Object.keys(templateCandidates).length === 1) {
    let template = Object.values(templateCandidates)[0];

    if (content === template) return;

    if (content === original) {
      composerBody.attrs.originalContent = template;
    } else {
      template = '\n\n' + template;
    }

    if (contentOverwrite) {
      app.composer.fields.content(template);
    } else {
      (app.composer.editor as any).insertAtCursor(template, false);
    }
  }
}

export default function configureTagTemplates() {
  extend(IndexPage.prototype, 'newDiscussionAction', function (promise: Promise<unknown>) {
    promise
      .then((composer: any) => {
        if (composer.fields?.tags?.length > 0) {
          insertTemplate();
        } else {
          const noTagTemplate = app.forum.attribute<string>('fof-discussion-templates.no_tag_template');
          if (noTagTemplate && composer.editor) {
            composer.editor.insertAtCursor(noTagTemplate, false);
          }
        }
      })
      .catch(() => {});
  });

  extend('ext:flarum/tags/forum/components/TagDiscussionModal', 'onremove', function () {
    if (app.composer.fields && (app.composer.fields as any).tags?.length > 0) {
      insertTemplate();
    }
  });

  override(ComposerState.prototype, 'show', function (this: ComposerState, originalFunction: () => void) {
    const body = this.body as any;
    if (body.componentClass === DiscussionComposer && this.fields?.content().trim() === '') {
      // Only insert template if the composer is empty

      if ((this.fields as any).tags) {
        // Insert if 1+ tags are selected
        insertTemplate(true);
      } else if (Array.isArray((this.fields as any).tags)) {
        // Insert if no tags are selected, but tags field present
        const noTagTemplate = app.forum.attribute<string>('fof-discussion-templates.no_tag_template');
        if (noTagTemplate && this.fields) {
          this.fields.content(noTagTemplate);
        }
      }
    }

    return originalFunction();
  });
}
