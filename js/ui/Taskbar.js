import { formatCash } from '../core/money.js';
import { createElement, escapeHtml } from './dom.js';
import { formatDate } from './format.js';
import { iconSvg } from './icons.js';

export class Taskbar {
  #element;
  #services;
  #windows;
  #actions;
  #taskKey = null;

  constructor(root, services, windows, actions) {
    this.#services = services;
    this.#windows = windows;
    this.#actions = actions;
    this.#element = createElement(this.#template());
    this.#element.addEventListener('click', event => this.#handleClick(event));
    root.append(this.#element);
  }

  setStartActive(isActive) {
    this.#element.querySelector('.start-button').classList.toggle('is-pressed', isActive);
  }

  update() {
    const { data } = this.#services.state;
    this.#setText('floppies', String(data.floppies));
    this.#setText('cash', formatCash(data.cash));
    this.#setText('date', formatDate(data.gameDay));
    this.#element.querySelector('[data-taskbar-action="mail"]').hidden = this.#services.mail.unreadCount() === 0;
    this.#renderTaskButtons();
  }

  #renderTaskButtons() {
    const windows = this.#windows.list();
    const key = windows.map(window => `${window.id}:${window.isFocused}`).join('|');
    if (key === this.#taskKey) return;

    this.#taskKey = key;
    this.#element.querySelector('.task-buttons').innerHTML = windows.map(window => `
      <button class="button task-button ${window.isFocused ? 'is-pressed' : ''}" data-taskbar-action="task" data-window-id="${window.id}">
        ${iconSvg(window.icon, 16)}<span>${escapeHtml(window.title)}</span>
      </button>`).join('');
  }

  #handleClick(event) {
    const button = event.target.closest('[data-taskbar-action]');
    if (!button) return;

    this.#services.sound.click();
    const handlers = {
      start: () => this.#actions.toggleStartMenu(),
      mail: () => this.#actions.openApp('mail'),
      floppies: () => this.#actions.openApp('appstore', { tab: 'shop' }),
      task: () => this.#windows.toggleFromTaskbar(button.dataset.windowId),
    };
    handlers[button.dataset.taskbarAction]?.();
  }

  #setText(name, text) {
    const element = this.#element.querySelector(`[data-tray="${name}"]`);
    if (element.textContent !== text) element.textContent = text;
  }

  #template() {
    return `
      <div class="taskbar">
        <button class="button start-button" data-taskbar-action="start">${iconSvg('logo', 16)}<span>Start</span></button>
        <div class="task-buttons"></div>
        <div class="tray">
          <button class="tray-button tray-mail" data-taskbar-action="mail" title="You've got mail" hidden>${iconSvg('mail', 16)}</button>
          <button class="tray-button" data-taskbar-action="floppies" title="Floppy Shop">${iconSvg('floppy', 16)}<span data-tray="floppies"></span></button>
          <span class="tray-cash" data-tray="cash"></span>
          <span class="tray-clock" data-tray="date"></span>
        </div>
      </div>`;
  }
}
