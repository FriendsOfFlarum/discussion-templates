import { IFormModalAttrs } from 'flarum/common/components/FormModal';
import FormModal from 'flarum/common/components/FormModal';
import Stream from 'flarum/common/utils/Stream';
import type Mithril from 'mithril';
import Discussion from 'flarum/common/models/Discussion';
export interface ReplyTemplateModalAttrs extends IFormModalAttrs {
    discussion: Discussion;
}
export default class ReplyTemplateModal extends FormModal<ReplyTemplateModalAttrs> {
    discussion: Discussion;
    replyTemplate: Stream<string>;
    oninit(vnode: Mithril.Vnode<ReplyTemplateModalAttrs, this>): void;
    className(): string;
    title(): string | any[];
    content(): JSX.Element;
    onsubmit(e: SubmitEvent): void;
}
