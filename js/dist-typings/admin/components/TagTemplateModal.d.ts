import { IFormModalAttrs } from 'flarum/common/components/FormModal';
import FormModal from 'flarum/common/components/FormModal';
import Stream from 'flarum/common/utils/Stream';
import type Mithril from 'mithril';
import Tag from 'ext:flarum/tags/common/models/Tag';
export interface TagTemplateModalAttrs extends IFormModalAttrs {
    model: Tag;
}
export default class TagTemplateModal extends FormModal<TagTemplateModalAttrs> {
    template: Stream<string>;
    oninit(vnode: Mithril.Vnode<TagTemplateModalAttrs, this>): void;
    className(): string;
    title(): string | any[];
    content(): JSX.Element[];
    changed(): boolean;
    onsubmit(e: SubmitEvent): void;
}
