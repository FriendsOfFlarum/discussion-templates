import 'flarum/common/models/Discussion';
import 'ext:flarum/tags/common/models/Tag';

declare module 'flarum/common/models/Discussion' {
  export default interface Discussion {
    replyTemplate(): string;
    canManageReplyTemplates(): boolean;
  }
}

declare module 'ext:flarum/tags/common/models/Tag' {
  export default interface Tag {
    template(): string;
  }
}
