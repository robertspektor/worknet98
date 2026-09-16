import { escapeHtml } from '../ui/dom.js';
import { formatEffect } from '../ui/format.js';
import { iconSvg } from '../ui/icons.js';
import { AppView } from './AppView.js';

export class InstalledApp extends AppView {
  #app;

  constructor(services, app) {
    super(services);
    this.#app = app;
  }

  render() {
    return `
      <div class="app-pad installed-app">
        <div class="company-header">
          ${iconSvg('program', 32)}
          <div>
            <h2 class="app-heading">${escapeHtml(this.#app.name)}</h2>
            <div class="muted">Running in the background</div>
          </div>
        </div>
        <p>${escapeHtml(this.#app.description)}</p>
        <p class="store-item-effect">Active: ${formatEffect(this.#app.effect)}</p>
        <div class="progress sunken"><div class="progress-bar is-indeterminate"></div></div>
      </div>`;
  }
}
