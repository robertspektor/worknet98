import { escapeHtml } from '../ui/dom.js';
import { iconSvg } from '../ui/icons.js';
import { AppView } from './AppView.js';

const DELETED_FILES = [
  'business_plan_FINAL_v7_really_final.doc',
  'work_life_balance.txt',
  'my_social_life.exe',
  'backup_of_backup_of_backup.zip',
];

export class RecycleBinApp extends AppView {
  render() {
    const files = DELETED_FILES.map(file => `<li>${iconSvg('program', 16)}<span>${escapeHtml(file)}</span></li>`).join('');

    return `
      <div class="app-pad recycle-bin">
        <ul class="file-list sunken">${files}</ul>
        <div class="app-actions app-actions-end">
          <button class="button" data-action="empty">Empty Recycle Bin</button>
        </div>
      </div>`;
  }

  handleAction(action) {
    if (action !== 'empty') return;
    this.services.sound.error();
    this.services.dialog.alert('Access denied', 'These files are protected by the Emotional Attachment Act of 1995.', 'warning');
  }
}
