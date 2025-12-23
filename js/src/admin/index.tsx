import app from 'flarum/admin/app';
import ExtensionPage from 'flarum/admin/components/ExtensionPage';
import extendEditTagModal from './extenders/extendEditTagModal';

export { default as extend } from './extend';

app.initializers.add('fof-discussion-templates', () => {
  app.extensionData
    .for('fof-discussion-templates')
    .registerSetting({
      setting: 'appendTemplateOnTagChange',
      label: app.translator.trans('fof-discussion-templates.admin.settings.append_template_on_tag_change'),
      help: app.translator.trans('fof-discussion-templates.admin.settings.append_template_on_tag_change_help'),
      type: 'boolean',
    })
    .registerSetting(function (this: ExtensionPage) {
      return (
        <div className="Form-group">
          <label>{app.translator.trans('fof-discussion-templates.admin.settings.no_tag_template')}</label>
          <textarea className="FormControl" rows="10" bidi={this.setting('fof-discussion-templates.no_tag_template')}></textarea>
        </div>
      );
    })
    .registerPermission(
      {
        icon: 'fas fa-reply',
        label: app.translator.trans('fof-discussion-templates.admin.permissions.manage_own_discussion_reply_templates'),
        permission: 'discussion.manageOwnDiscussionReplyTemplates',
      },
      'start',
      3
    )
    .registerPermission(
      {
        icon: 'fas fa-reply',
        label: app.translator.trans('fof-discussion-templates.admin.permissions.manage_all_reply_templates'),
        permission: 'discussion.manageAllReplyTemplates',
      },
      'moderate',
      3
    );

  extendEditTagModal();
});
