import { createElement, escapeHtml } from './dom.js';
import { glyphSvg, iconSvg } from './icons.js';

const CLOSE_INDEX = -1;

export class Dialog {
  #host = null;

  attachTo(host) {
    this.#host = host;
  }

  detach() {
    this.#host = null;
  }

  show({ title, body, icon = null, buttons = [{ label: 'OK', value: true, primary: true }], onOpen = null }) {
    if (!this.#host) return Promise.resolve(null);

    return new Promise(resolve => {
      const overlay = createElement(this.#template(title, body, icon, buttons));
      overlay.addEventListener('click', event => {
        const button = event.target.closest('[data-dialog-index]');
        if (!button || button.disabled) return;

        const index = Number(button.dataset.dialogIndex);
        overlay.remove();
        resolve(index === CLOSE_INDEX ? null : buttons[index].value);
      });

      this.#host.append(overlay);
      overlay.querySelector('.button-primary:not(:disabled)')?.focus();
      onOpen?.(overlay);
    });
  }

  alert(title, message, icon = 'info') {
    return this.show({ title, icon, body: `<p>${escapeHtml(message)}</p>` });
  }

  async confirm(title, message, confirmLabel = 'OK', icon = 'warning') {
    const answer = await this.show({
      title,
      icon,
      body: `<p>${escapeHtml(message)}</p>`,
      buttons: [
        { label: confirmLabel, value: true, primary: true },
        { label: 'Cancel', value: false },
      ],
    });

    return Boolean(answer);
  }

  #template(title, body, icon, buttons) {
    const buttonHtml = buttons.map((button, index) => `
      <button class="button ${button.primary ? 'button-primary' : ''}" data-dialog-index="${index}" ${button.disabled ? 'disabled' : ''}>
        ${escapeHtml(button.label)}
      </button>`).join('');

    return `
      <div class="dialog-overlay">
        <section class="window dialog is-focused" role="alertdialog" aria-label="${escapeHtml(title)}">
          <header class="title-bar">
            <span class="title-bar-text"><span>${escapeHtml(title)}</span></span>
            <button class="title-button" data-dialog-index="${CLOSE_INDEX}" aria-label="Close">${glyphSvg('close')}</button>
          </header>
          <div class="dialog-content">
            ${icon ? `<div class="dialog-icon">${iconSvg(icon, 32)}</div>` : ''}
            <div class="dialog-body">${body}</div>
          </div>
          <div class="dialog-buttons">${buttonHtml}</div>
        </section>
      </div>`;
  }
}
