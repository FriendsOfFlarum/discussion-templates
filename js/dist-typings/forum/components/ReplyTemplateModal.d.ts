/// <reference types="flarum/@types/translator-icu-rich" />
import Modal, { IInternalModalAttrs } from 'flarum/common/components/Modal';
import Stream from 'flarum/common/utils/Stream';
import type Mithril from 'mithril';
import Discussion from 'flarum/common/models/Discussion';
export interface ReplyTemplateModalAttrs extends IInternalModalAttrs {
    discussion: Discussion;
}
export default class ReplyTemplateModal extends Modal<ReplyTemplateModalAttrs> {
    discussion: Discussion;
    replyTemplate: Stream<string>;
    oninit(vnode: Mithril.Vnode<ReplyTemplateModalAttrs, this>): void;
    className(): string;
    title(): import("@askvortsov/rich-icu-message-formatter").NestedStringArray;
    content(): JSX.Element;
    onsubmit(e: SubmitEvent): void;
}
