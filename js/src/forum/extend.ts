import Extend from 'flarum/common/extenders';
import Discussion from 'flarum/common/models/Discussion';
import commonExtend from '../common/extend';

export default [
  ...commonExtend,

  new Extend.Model(Discussion) //
    .attribute<string>('replyTemplate')
    .attribute<boolean>('canManageReplyTemplates'),
];
