import { formatCash, formatRate } from '../../core/money.js';
import { GAME_CONFIG } from '../../data/config.js';
import { findIndustry, INDUSTRIES } from '../../data/industries.js';
import { escapeHtml } from '../../ui/dom.js';
import { floppyAmountHtml, formatDate } from '../../ui/format.js';
import { iconSvg } from '../../ui/icons.js';

function industryOption(industry, index) {
  return `
    <label class="industry-option">
      <input type="radio" name="industry" value="${industry.id}" ${index === 0 ? 'checked' : ''}>
      <span class="industry-name">${escapeHtml(industry.name)}</span>
      <span class="industry-tagline">${escapeHtml(industry.tagline)}</span>
      <span class="industry-stats">
        Costs ${formatCash(industry.foundingCost)} · ${formatCash(industry.clickValue)} per click · ${formatRate(industry.employeeIncome)} per employee
      </span>
    </label>`;
}

export function renderFoundingForm() {
  return `
    <div class="app-pad company-founding">
      <h2 class="app-heading">Found your empire</h2>
      <p class="muted">Every billion-dollar company started in a garage. You have a monitor. Close enough.</p>
      <label class="field">
        <span>Company name</span>
        <input class="input" name="companyName" maxlength="32" value="Garage Startup Inc." autocomplete="off">
      </label>
      <fieldset class="group-box">
        <legend>Industry</legend>
        ${INDUSTRIES.map(industryOption).join('')}
      </fieldset>
      <div class="app-actions app-actions-end">
        <button class="button button-primary" data-action="found">Found company</button>
      </div>
    </div>`;
}

function stat(label, bind) {
  return `<div class="stat sunken"><span class="stat-label">${label}</span><span class="stat-value" data-bind="${bind}"></span></div>`;
}

export function renderDashboard(state) {
  const industry = findIndustry(state.company.industryId);

  return `
    <div class="app-pad company-dashboard">
      <div class="company-header">
        ${iconSvg('briefcase', 32)}
        <div>
          <h2 class="app-heading">${escapeHtml(state.company.name)}</h2>
          <div class="muted">${escapeHtml(industry.name)} · founded ${formatDate(state.company.foundedDay)}</div>
        </div>
      </div>
      <div class="stat-grid">
        ${stat('Cash', 'cash')}
        ${stat('Income', 'income')}
        ${stat('Employees', 'employees')}
        ${stat('Per click', 'click')}
      </div>
      <div class="goal">
        <div class="goal-label"><span>Road to IPO</span><span data-bind="goal"></span></div>
        <div class="progress sunken"><div class="progress-bar" data-progress></div></div>
      </div>
      <div class="boost-banner" data-boost hidden>2x SPONSOR BOOST · <span data-bind="boostSeconds"></span>s left</div>
      <button class="button work-button" data-action="work">WORK HARD</button>
      <div class="app-actions">
        <button class="button" data-action="hire"><span>Hire employee (<span data-bind="hireCost"></span>)</span></button>
        <button class="button" data-action="warp"><span>Time Warp +30 min</span>${floppyAmountHtml(GAME_CONFIG.timeWarpCost)}</button>
      </div>
      <p class="tip" data-bind="tip"></p>
    </div>`;
}
