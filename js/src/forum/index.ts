import app from 'flarum/forum/app';
import configureReplyTemplates from './configureReplyTemplates';
import configureTagTemplates from './configureTagTemplates';

app.initializers.add('fof-discussion-templates', () => {
  configureReplyTemplates();
  configureTagTemplates();
});
