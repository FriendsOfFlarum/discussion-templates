import app from 'flarum/admin/app';
import Button from 'flarum/common/components/Button';
import Modal, { IInternalModalAttrs } from 'flarum/common/components/Modal';
import Stream from 'flarum/common/utils/Stream';
import type Mithril from 'mithril';
import Tag from 'flarum/tags/common/models/Tag';

export interface TagTemplateModalAttrs extends IInternalModalAttrs {
  model: Tag;
}

export default class TagTemplateModal extends Modal<TagTemplateModalAttrs> {
  template!: Stream<string>;

  oninit(vnode: Mithril.Vnode<TagTemplateModalAttrs, this>) {
    super.oninit(vnode);

    this.template = Stream(this.attrs.model.template());
  }

  className() {
    return 'TagTemplateModal Modal--large';
  }

  title() {
    return app.translator.trans('fof-discussion-templates.admin.tag_template_modal.title');
  }

  content() {
    return [
      <div className="Modal-body">
        <div className="Form">
          <p>{app.translator.trans('fof-discussion-templates.admin.tag_template_modal.customize_text')}</p>
          <div className="Form-group">
            <textarea className="FormControl" rows="30" bidi={this.template} />
          </div>
          <Button type="submit" className="Button Button--primary" loading={this.loading} disabled={!this.changed()}>
            {app.translator.trans('fof-discussion-templates.admin.tag_template_modal.submit_button')}
          </Button>
        </div>
      </div>,
    ];
  }

  changed(): boolean {
    return this.template() !== this.attrs.model.template();
  }

  onsubmit(e: SubmitEvent) {
    e.preventDefault();

    const tag = this.attrs.model;
    const template = this.template();

    this.loading = true;

    app
      .request({
        method: 'PATCH',
        url: app.forum.attribute('apiUrl') + '/tags/' + tag.id() + '/template',
        body: { data: { template } },
      })
      .then(() => {
        if (tag.data?.attributes) {
          tag.data.attributes.template = template;
        }
        app.modal.close();
      });
  }
}
