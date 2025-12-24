import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import Button from 'flarum/common/components/Button';
import DiscussionControls from 'flarum/forum/utils/DiscussionControls';

import ReplyTemplateModal from './components/ReplyTemplateModal';

export default function configureReplyTemplates() {
  extend(ReplyComposer, 'initAttrs', function (_, attrs) {
    if (!attrs.originalContent) {
      attrs.originalContent = attrs.discussion.replyTemplate();
    }
  });

  extend(DiscussionControls, 'userControls', function (items, discussion) {
    if (!app.session.user || !discussion.canManageReplyTemplates()) return;

    items.add(
      'reply-template',
      <Button
        icon="fas fa-reply"
        onclick={() =>
          app.modal.show(ReplyTemplateModal, {
            discussion,
          })
        }
      >
        {app.translator.trans('fof-discussion-templates.forum.discussion_controls.reply_template_button')}
      </Button>
    );
  });
}
