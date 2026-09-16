import { createElement, escapeHtml, renderScale } from './dom.js';
import { glyphSvg, iconSvg } from './icons.js';

const CASCADE_STEP = 24;
const DRAG_KEEP_VISIBLE = 60;

export class WindowManager {
  #layer;
  #onChange;
  #windows = new Map();
  #zIndex = 1;
  #focusedId = null;

  constructor(layer, onChange) {
    this.#layer = layer;
    this.#onChange = onChange;
  }

  open(definition, createView, options = {}) {
    if (this.#windows.has(definition.id)) {
      this.#restore(definition.id);
      this.#windows.get(definition.id).view.receiveOptions(options);
      return;
    }

    const element = createElement(this.#template(definition));
    const view = createView();
    this.#layer.append(element);
    this.#place(element, definition);
    this.#windows.set(definition.id, { definition, element, view, minimized: false });
    this.#bindChrome(definition.id, element);

    view.mount(element.querySelector('.window-body'));
    view.receiveOptions(options);
    this.focus(definition.id);
  }

  focus(id) {
    const entry = this.#windows.get(id);
    if (!entry) return;

    this.#focusedId = id;
    entry.element.style.zIndex = String(++this.#zIndex);
    this.#windows.forEach((other, otherId) => other.element.classList.toggle('is-focused', otherId === id));
    this.#onChange();
  }

  close(id) {
    const entry = this.#windows.get(id);
    if (!entry) return;

    entry.view.destroy();
    entry.element.remove();
    this.#windows.delete(id);
    if (this.#focusedId === id) this.#focusTopmost();
    this.#onChange();
  }

  closeAll() {
    [...this.#windows.keys()].forEach(id => this.close(id));
  }

  toggleFromTaskbar(id) {
    const entry = this.#windows.get(id);
    if (!entry) return;

    if (entry.minimized || this.#focusedId !== id) {
      this.#restore(id);
    } else {
      this.#minimize(id);
    }
  }

  updateAll() {
    this.#windows.forEach(entry => entry.view.update());
  }

  list() {
    return [...this.#windows.values()].map(({ definition, minimized }) => ({
      id: definition.id,
      title: definition.title,
      icon: definition.icon,
      isFocused: definition.id === this.#focusedId && !minimized,
    }));
  }

  #restore(id) {
    const entry = this.#windows.get(id);
    entry.minimized = false;
    entry.element.classList.remove('is-minimized');
    this.focus(id);
  }

  #minimize(id) {
    const entry = this.#windows.get(id);
    entry.minimized = true;
    entry.element.classList.remove('is-focused');
    entry.element.classList.add('is-minimized');
    this.#focusTopmost();
    this.#onChange();
  }

  #focusTopmost() {
    const visible = [...this.#windows.entries()].filter(([, entry]) => !entry.minimized);
    const topmost = visible.sort(([, a], [, b]) => Number(b.element.style.zIndex) - Number(a.element.style.zIndex))[0];
    this.#focusedId = null;
    if (topmost) this.focus(topmost[0]);
  }

  #place(element, definition) {
    const width = Math.min(definition.width, this.#layer.offsetWidth - 8);
    const height = Math.min(definition.height, this.#layer.offsetHeight - 8);
    const offset = this.#windows.size * CASCADE_STEP;

    element.style.width = `${width}px`;
    element.style.height = `${height}px`;
    element.style.left = `${Math.max(0, Math.min(110 + offset, this.#layer.offsetWidth - width))}px`;
    element.style.top = `${Math.max(0, Math.min(16 + offset, this.#layer.offsetHeight - height))}px`;
  }

  #bindChrome(id, element) {
    element.addEventListener('pointerdown', () => {
      if (this.#focusedId !== id) this.focus(id);
    });
    element.querySelector('[data-window-action="minimize"]').addEventListener('click', () => this.#minimize(id));
    element.querySelector('[data-window-action="close"]').addEventListener('click', () => this.close(id));
    this.#enableDragging(element);
  }

  #enableDragging(element) {
    const titleBar = element.querySelector('.title-bar');

    titleBar.addEventListener('pointerdown', event => {
      if (event.button !== 0 || event.target.closest('button')) return;

      const scale = renderScale(this.#layer);
      const start = { x: event.clientX, y: event.clientY, left: element.offsetLeft, top: element.offsetTop };
      titleBar.setPointerCapture(event.pointerId);

      const move = moveEvent => {
        const left = start.left + (moveEvent.clientX - start.x) / scale;
        const top = start.top + (moveEvent.clientY - start.y) / scale;
        element.style.left = `${this.#clamp(left, DRAG_KEEP_VISIBLE - element.offsetWidth, this.#layer.offsetWidth - DRAG_KEEP_VISIBLE)}px`;
        element.style.top = `${this.#clamp(top, 0, this.#layer.offsetHeight - titleBar.offsetHeight)}px`;
      };
      const stop = () => {
        titleBar.removeEventListener('pointermove', move);
        titleBar.removeEventListener('pointerup', stop);
        titleBar.removeEventListener('pointercancel', stop);
      };

      titleBar.addEventListener('pointermove', move);
      titleBar.addEventListener('pointerup', stop);
      titleBar.addEventListener('pointercancel', stop);
    });
  }

  #clamp(value, min, max) {
    return Math.max(min, Math.min(max, value));
  }

  #template({ title, icon }) {
    return `
      <section class="window" aria-label="${escapeHtml(title)}">
        <header class="title-bar">
          <span class="title-bar-text">${iconSvg(icon, 16)}<span>${escapeHtml(title)}</span></span>
          <button class="title-button" data-window-action="minimize" aria-label="Minimize">${glyphSvg('minimize')}</button>
          <button class="title-button" data-window-action="close" aria-label="Close">${glyphSvg('close')}</button>
        </header>
        <div class="window-body"></div>
      </section>`;
  }
}
