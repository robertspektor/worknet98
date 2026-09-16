import { createElement, escapeHtml } from './dom.js';
import { iconSvg } from './icons.js';

const VISIBLE_MILLISECONDS = 5000;

export class Toast {
  #element;
  #timer = null;
  #onClick = null;

  constructor(root) {
    this.#element = createElement('<button class="toast" role="status" hidden></button>');
    this.#element.addEventListener('click', () => {
      this.#onClick?.();
      this.hide();
    });
    root.append(this.#element);
  }

  show({ title, message, icon = 'mail', onClick = null }) {
    this.#onClick = onClick;
    this.#element.innerHTML = `
      <span class="toast-title">${iconSvg(icon, 16)}<span>${escapeHtml(title)}</span></span>
      <span class="toast-message">${escapeHtml(message)}</span>`;
    this.#element.hidden = false;

    clearTimeout(this.#timer);
    this.#timer = setTimeout(() => this.hide(), VISIBLE_MILLISECONDS);
  }

  hide() {
    clearTimeout(this.#timer);
    this.#element.hidden = true;
  }
}
