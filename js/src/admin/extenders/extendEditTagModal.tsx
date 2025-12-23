import app from 'flarum/admin/app';
import { extend } from 'flarum/common/extend';
import Button from 'flarum/common/components/Button';
import EditTagModal from 'flarum/tags/admin/components/EditTagModal';
import TagTemplateModal from '../components/TagTemplateModal';

export default function extendEditTagModal() {
  extend(EditTagModal.prototype, 'fields', function (items) {
    if (this.tag.id()) {
      items.add(
        'tag-template-modal-button',
        <fieldset>
          <legend>{app.translator.trans('fof-discussion-templates.admin.tags.tag_template_heading')}</legend>
          <div className="helpText">{app.translator.trans('fof-discussion-templates.admin.tags.tag_template_text')}</div>
          <Button
            className="Button Button--primary"
            onclick={() => {
              app.modal.show(TagTemplateModal, { model: this.tag });
            }}
          >
            {app.translator.trans('fof-discussion-templates.admin.tags.tag_template_button')}
          </Button>
        </fieldset>,
        -20
      );
    }
  });
}
