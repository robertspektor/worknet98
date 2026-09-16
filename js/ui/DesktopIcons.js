import { escapeHtml } from './dom.js';
import { iconSvg } from './icons.js';

export class DesktopIcons {
  #container;
  #openApp;
  #key = null;

  constructor(container, openApp) {
    this.#container = container;
    this.#openApp = openApp;
    container.parentElement.addEventListener('pointerdown', event => this.#select(event));
    container.addEventListener('dblclick', event => this.#open(event));
    container.addEventListener('keydown', event => event.key === 'Enter' && this.#open(event));
    container.addEventListener('pointerup', event => event.pointerType === 'touch' && this.#open(event));
  }

  update(entries) {
    const key = entries.map(entry => entry.id).join('|');
    if (key === this.#key) return;

    this.#key = key;
    this.#container.innerHTML = entries.map(entry => `
      <button class="desktop-icon" data-app-id="${entry.id}">
        ${iconSvg(entry.icon, 32)}
        <span class="desktop-icon-label">${escapeHtml(entry.title)}</span>
      </button>`).join('');
  }

  #select(event) {
    const icon = event.target.closest('.desktop-icon');
    if (!icon && event.target.closest('.window, .taskbar, .start-menu, .dialog-overlay')) return;

    this.#container.querySelectorAll('.is-selected').forEach(element => element.classList.remove('is-selected'));
    icon?.classList.add('is-selected');
  }

  #open(event) {
    const icon = event.target.closest('.desktop-icon');
    if (icon) this.#openApp(icon.dataset.appId);
  }
}
