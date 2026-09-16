import { failure, success } from '../core/result.js';
import { findMail, MAILS } from '../data/mails.js';

export class MailService {
  #state;
  #bus;

  constructor(state, bus) {
    this.#state = state;
    this.#bus = bus;
  }

  tick() {
    const data = this.#state.data;
    const dueMails = MAILS.filter(mail => !this.#findEntry(data, mail.id) && mail.trigger(data));
    if (dueMails.length === 0) return;

    this.#state.update(state => {
      dueMails.forEach(mail => state.inbox.push({ id: mail.id, read: false, actionDone: false, receivedDay: state.gameDay }));
    });
    dueMails.forEach(mail => this.#bus.emit('mail:received', mail));
  }

  unreadCount() {
    return this.#state.data.inbox.filter(entry => !entry.read).length;
  }

  markRead(id) {
    if (this.#findEntry(this.#state.data, id)?.read !== false) return;
    this.#state.update(state => { this.#findEntry(state, id).read = true; });
  }

  runAction(id) {
    const action = findMail(id)?.action;
    const entry = this.#findEntry(this.#state.data, id);
    if (!action || !entry || entry.actionDone) return failure('');

    if (action.type === 'openApp') {
      this.#bus.emit('app:open', { appId: action.appId, options: action.options ?? {} });
      return success();
    }

    const cashChange = action.type === 'scam' ? -Math.min(action.amount, this.#state.data.cash) : action.amount;
    this.#state.update(state => {
      state.cash += cashChange;
      this.#findEntry(state, id).actionDone = true;
    });

    return success(action.result);
  }

  #findEntry(state, id) {
    return state.inbox.find(entry => entry.id === id);
  }
}
