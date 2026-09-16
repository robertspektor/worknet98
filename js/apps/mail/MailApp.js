import { AppView } from '../AppView.js';
import { renderMail } from './mailTemplates.js';

export class MailApp extends AppView {
  #selectedId = null;

  receiveOptions({ mailId } = {}) {
    if (mailId) this.#select(mailId);
  }

  structureKey() {
    const inboxKey = this.data.inbox.map(entry => `${entry.id}:${entry.read}:${entry.actionDone}`).join(',');
    return `${this.#selectedId}|${inboxKey}`;
  }

  render() {
    return renderMail(this.data.inbox, this.#selectedId);
  }

  handleAction(action, element) {
    const handlers = {
      select: () => this.#select(element.dataset.mailId),
      runMailAction: () => this.#runSelectedAction(),
    };
    handlers[action]?.();
  }

  #select(mailId) {
    this.#selectedId = mailId;
    this.services.mail.markRead(mailId);
    this.update();
  }

  async #runSelectedAction() {
    const result = this.services.mail.runAction(this.#selectedId);
    if (!result.message) return;

    this.services.sound.cash();
    await this.services.dialog.alert('Mail', result.message);
  }
}
