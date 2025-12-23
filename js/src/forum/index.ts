import app from 'flarum/forum/app';
import configureReplyTemplates from './configureReplyTemplates';
import configureTagTemplates from './configureTagTemplates';

export { default as extend } from './extend';

app.initializers.add('fof-discussion-templates', () => {
  configureReplyTemplates();
  configureTagTemplates();
});
