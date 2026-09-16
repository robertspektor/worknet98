import { wait } from './dom.js';
import { iconSvg } from './icons.js';

const BIOS_LINES = [
  'RetroTron Modular BIOS v1.07',
  'Copyright (C) 1995, RetroTron Corporation',
  '',
  'Main Processor : Pentium-ish 75MHz',
  'Memory Test    : 16384K OK',
  '',
  'Detecting Floppy Drive A: ... 1.44M',
  'Detecting Hard Disk C:    ... 540MB',
  'Detecting Ambition        ... NOT FOUND',
  'Detecting Ambition        ... retrying ... OK',
  '',
  'Starting CorpOS 95 ...',
];

const SPLASH_STATUSES = [
  'Loading synergy...',
  'Calibrating buzzwords...',
  'Inflating valuation...',
  'Almost there...',
];

const BIOS_LINE_DELAY = 150;
const SPLASH_STATUS_DELAY = 650;

export class BootScreen {
  async run(viewport, isActive) {
    return await this.#showBios(viewport, isActive) && await this.#showSplash(viewport, isActive);
  }

  async #showBios(viewport, isActive) {
    viewport.innerHTML = '<div class="bios"><pre class="bios-text"></pre></div>';
    const output = viewport.querySelector('.bios-text');

    for (const line of BIOS_LINES) {
      output.textContent += `${line}\n`;
      await wait(BIOS_LINE_DELAY);
      if (!isActive()) return false;
    }

    return true;
  }

  async #showSplash(viewport, isActive) {
    viewport.innerHTML = `
      <div class="splash">
        <div class="splash-brand">
          ${iconSvg('logo', 96)}
          <div class="splash-title">Corp<b>OS</b><sup>95</sup></div>
          <div class="splash-subtitle">Business Edition</div>
        </div>
        <div class="splash-status"></div>
        <div class="splash-bar"><span></span></div>
      </div>`;
    const status = viewport.querySelector('.splash-status');

    for (const text of SPLASH_STATUSES) {
      status.textContent = text;
      await wait(SPLASH_STATUS_DELAY);
      if (!isActive()) return false;
    }

    return true;
  }
}
