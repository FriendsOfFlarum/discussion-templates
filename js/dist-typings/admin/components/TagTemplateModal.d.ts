/// <reference types="flarum/@types/translator-icu-rich" />
import Modal, { IInternalModalAttrs } from 'flarum/common/components/Modal';
import Stream from 'flarum/common/utils/Stream';
import type Mithril from 'mithril';
import Tag from 'flarum/tags/common/models/Tag';
export interface TagTemplateModalAttrs extends IInternalModalAttrs {
    model: Tag;
}
export default class TagTemplateModal extends Modal<TagTemplateModalAttrs> {
    template: Stream<string>;
    oninit(vnode: Mithril.Vnode<TagTemplateModalAttrs, this>): void;
    className(): string;
    title(): import("@askvortsov/rich-icu-message-formatter").NestedStringArray;
    content(): JSX.Element[];
    changed(): boolean;
    onsubmit(e: SubmitEvent): void;
}
