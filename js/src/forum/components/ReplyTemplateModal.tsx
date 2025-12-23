import app from 'flarum/forum/app';
import Modal, { IInternalModalAttrs } from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';
import Stream from 'flarum/common/utils/Stream';
import type Mithril from 'mithril';
import Discussion from 'flarum/common/models/Discussion';

export interface ReplyTemplateModalAttrs extends IInternalModalAttrs {
  discussion: Discussion;
}

export default class ReplyTemplateModal extends Modal<ReplyTemplateModalAttrs> {
  discussion!: Discussion;
  replyTemplate!: Stream<string>;

  oninit(vnode: Mithril.Vnode<ReplyTemplateModalAttrs, this>) {
    super.oninit(vnode);

    this.discussion = this.attrs.discussion;
    this.replyTemplate = Stream(this.discussion.replyTemplate());
  }

  className() {
    return 'ReplyTemplateModal Modal';
  }

  title() {
    return app.translator.trans('fof-discussion-templates.forum.reply_template.title');
  }

  content() {
    return (
      <div className="Modal-body">
        <div className="Form Form--centered">
          <div className="Form-group">
            <textarea className="FormControl" bidi={this.replyTemplate} rows="6" />
          </div>
          <div className="Form-group">
            {Button.component(
              {
                className: 'Button Button--primary Button--block',
                type: 'submit',
                loading: this.loading,
              },
              app.translator.trans('fof-discussion-templates.forum.reply_template.submit_button')
            )}
          </div>
        </div>
      </div>
    );
  }

  onsubmit(e: SubmitEvent) {
    e.preventDefault();

    this.loading = true;

    const replyTemplate = this.replyTemplate();

    if (replyTemplate !== this.discussion.replyTemplate()) {
      this.discussion
        .save({ replyTemplate })
        .then(() => {
          m.redraw();
          this.hide();
        })
        .catch(() => {
          this.loading = false;
          m.redraw();
        });
    } else {
      this.hide();
    }
  }
}
