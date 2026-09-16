import { formatCash, formatRate } from '../../core/money.js';
import { GAME_CONFIG } from '../../data/config.js';
import { boostSecondsLeft, clickValue, goalProgress, hireCost, incomePerSecond } from '../../services/EconomyCalculator.js';
import { renderScale } from '../../ui/dom.js';
import { AppView } from '../AppView.js';
import { renderDashboard, renderFoundingForm } from './companyTemplates.js';

const TIPS = [
  'Tip: "Synergy" is a real word. Say it in every meeting.',
  'Tip: AppStore software pays for itself. Mostly.',
  'Tip: Floppies make everything faster. That is just science.',
  'Tip: Employees work harder when you install things.',
  'Tip: Never reply to princes. Or do. It is your money.',
];
const TIP_ROTATION_MS = 8000;
const FLOAT_TEXT_MS = 800;

export class CompanyApp extends AppView {
  structureKey() {
    return this.data.company ? 'dashboard' : 'founding';
  }

  render() {
    return this.data.company ? renderDashboard(this.data) : renderFoundingForm();
  }

  refresh() {
    if (this.data.company) this.#refreshDashboard(Date.now());
  }

  handleAction(action, element, event) {
    const handlers = {
      found: () => this.#foundCompany(),
      work: () => this.#work(element, event),
      hire: () => this.#hire(),
      warp: () => this.#timeWarp(),
    };
    handlers[action]?.();
  }

  #refreshDashboard(now) {
    const { data } = this;
    const boostSeconds = boostSecondsLeft(data, now);

    this.bindText('cash', formatCash(data.cash));
    this.bindText('income', formatRate(incomePerSecond(data, now)));
    this.bindText('employees', String(data.employees));
    this.bindText('click', formatCash(clickValue(data, now)));
    this.bindText('hireCost', formatCash(hireCost(data)));
    this.bindText('goal', `${formatCash(data.cash)} / ${formatCash(GAME_CONFIG.ipoGoal)}`);
    this.bindText('boostSeconds', String(boostSeconds));
    this.bindText('tip', TIPS[Math.floor(now / TIP_ROTATION_MS) % TIPS.length]);
    this.body.querySelector('[data-progress]').style.width = `${goalProgress(data) * 100}%`;
    this.body.querySelector('[data-boost]').hidden = boostSeconds === 0;
    this.setDisabled('hire', data.cash < hireCost(data));
  }

  async #foundCompany() {
    const name = this.body.querySelector('[name="companyName"]').value;
    const industryId = this.body.querySelector('[name="industry"]:checked')?.value;
    const result = this.services.economy.foundCompany(name, industryId);

    if (result.ok) {
      this.services.sound.fanfare();
      return;
    }
    this.services.sound.error();
    await this.services.dialog.alert('Hold on', result.message, 'warning');
  }

  #work(button, event) {
    const earned = this.services.economy.work();
    this.services.sound.cash();
    this.#spawnFloatingText(button, event, `+${formatCash(earned)}`);
  }

  #hire() {
    const result = this.services.economy.hire();
    if (!result.ok) this.services.sound.error();
  }

  async #timeWarp() {
    const { services } = this;
    if (this.data.floppies < GAME_CONFIG.timeWarpCost) {
      const wantsFloppies = await services.dialog.confirm('Not enough floppies', 'Time travel runs on floppies. Get some in the Floppy Shop?', 'Get floppies', 'floppy');
      if (wantsFloppies) services.bus.emit('app:open', { appId: 'appstore', options: { tab: 'shop' } });
      return;
    }

    const result = services.monetization.timeWarp();
    if (result.ok) services.sound.cash();
    await services.dialog.alert('Time Warp', result.message, result.ok ? 'info' : 'warning');
  }

  #spawnFloatingText(button, event, text) {
    const bounds = button.getBoundingClientRect();
    const scale = renderScale(button);
    const isPointer = event.detail > 0;
    const floating = document.createElement('span');

    floating.className = 'float-text';
    floating.textContent = text;
    floating.style.left = `${isPointer ? (event.clientX - bounds.left) / scale : button.offsetWidth / 2}px`;
    floating.style.top = `${isPointer ? (event.clientY - bounds.top) / scale : button.offsetHeight / 2}px`;
    button.append(floating);
    setTimeout(() => floating.remove(), FLOAT_TEXT_MS);
  }
}
