import { createElement } from './dom.js';
import { iconSvg } from './icons.js';

const MENU_ITEMS = [
  { command: 'openApp', appId: 'company', label: 'My Company', icon: 'briefcase' },
  { command: 'openApp', appId: 'appstore', label: 'AppStore', icon: 'store' },
  { command: 'openApp', appId: 'mail', label: 'Mail', icon: 'mail' },
  { command: 'openApp', appId: 'recycle', label: 'Recycle Bin', icon: 'trash' },
  { separator: true },
  { command: 'resetGame', label: 'Reset Game...', icon: 'warning' },
  { command: 'shutDown', label: 'Shut Down...', icon: 'computer' },
];

export class StartMenu {
  #element;
  #commands;
  #onVisibilityChange;

  constructor(root, commands, onVisibilityChange) {
    this.#commands = commands;
    this.#onVisibilityChange = onVisibilityChange;
    this.#element = createElement(this.#template());
    this.#element.addEventListener('click', event => this.#handleClick(event));
    root.addEventListener('pointerdown', event => this.#closeOnOutsideClick(event));
    root.append(this.#element);
  }

  toggle() {
    this.#setVisible(this.#element.hidden);
  }

  #setVisible(isVisible) {
    this.#element.hidden = !isVisible;
    this.#onVisibilityChange(isVisible);
  }

  #handleClick(event) {
    const item = event.target.closest('[data-command]');
    if (!item) return;

    this.#setVisible(false);
    this.#commands[item.dataset.command](item.dataset.appId);
  }

  #closeOnOutsideClick(event) {
    if (this.#element.hidden) return;
    if (this.#element.contains(event.target) || event.target.closest('.start-button')) return;
    this.#setVisible(false);
  }

  #template() {
    const items = MENU_ITEMS.map(item => item.separator
      ? '<li class="start-menu-separator" role="separator"></li>'
      : `<li><button class="start-menu-item" data-command="${item.command}" data-app-id="${item.appId ?? ''}">${iconSvg(item.icon, 24)}<span>${item.label}</span></button></li>`);

    return `
      <nav class="start-menu" hidden>
        <div class="start-menu-side"><span>Corp<b>OS</b> 95</span></div>
        <ul class="start-menu-items">${items.join('')}</ul>
      </nav>`;
  }
}
