import { formatEuro } from '../../core/money.js';
import { GAME_CONFIG } from '../../data/config.js';
import { FLOPPY_PACKS, STARTER_PACK } from '../../data/offers.js';
import { STORE_APPS } from '../../data/storeApps.js';
import { escapeHtml } from '../../ui/dom.js';
import { formatEffect, priceHtml } from '../../ui/format.js';
import { iconSvg } from '../../ui/icons.js';

const TABS = [
  { id: 'apps', label: 'Apps' },
  { id: 'shop', label: 'Floppy Shop' },
];

function renderTabs(activeTab) {
  return `
    <div class="tabs" role="tablist">
      ${TABS.map(tab => `
        <button class="tab ${tab.id === activeTab ? 'is-active' : ''}" role="tab" data-action="tab" data-tab="${tab.id}">${tab.label}</button>`).join('')}
    </div>`;
}

function renderStoreApp(app, isInstalled) {
  const button = isInstalled
    ? '<button class="button" disabled>Installed</button>'
    : `<button class="button store-price" data-action="install" data-app-id="${app.id}">${priceHtml(app.price)}</button>`;

  return `
    <article class="store-item sunken">
      ${iconSvg('program', 32)}
      <div class="store-item-text">
        <div class="store-item-name">${escapeHtml(app.name)}${app.price.currency === 'floppies' ? '<span class="premium-badge">PREMIUM</span>' : ''}</div>
        <div class="store-item-description">${escapeHtml(app.description)}</div>
        <div class="store-item-effect">${formatEffect(app.effect)}</div>
      </div>
      ${button}
    </article>`;
}

function renderAppsPanel(state) {
  return STORE_APPS.map(app => renderStoreApp(app, state.installedApps.includes(app.id))).join('');
}

function renderStarterPack() {
  return `
    <section class="offer-hero">
      <span class="ribbon">ONE-TIME OFFER</span>
      ${iconSvg('floppy', 48)}
      <div class="offer-text">
        <strong>${STARTER_PACK.name}</strong>
        <span>${STARTER_PACK.floppies} floppies and ${formatEffect(STARTER_PACK.effect)}. Forever.</span>
      </div>
      <button class="button button-primary" data-action="starter">${formatEuro(STARTER_PACK.priceCents)}</button>
    </section>`;
}

function renderAdOffer() {
  return `
    <section class="ad-offer sunken">
      ${iconSvg('computer', 32)}
      <div class="offer-text">
        <strong>Watch a sponsored message</strong>
        <span>+${GAME_CONFIG.adReward} floppies and 2x income for ${GAME_CONFIG.adBoostSeconds}s</span>
      </div>
      <button class="button" data-action="ad" data-bind="adLabel">Watch ad</button>
    </section>`;
}

function renderPack(pack) {
  const icons = Array.from({ length: pack.iconCount }, () => iconSvg('floppy', 32)).join('');

  return `
    <article class="pack sunken">
      ${pack.badge ? `<span class="ribbon">${pack.badge}</span>` : ''}
      <div class="pack-icons">${icons}</div>
      <div class="pack-amount">${pack.floppies}</div>
      <div class="pack-name">${escapeHtml(pack.name)}</div>
      <button class="button" data-action="pack" data-pack-id="${pack.id}">${formatEuro(pack.priceCents)}</button>
    </article>`;
}

function renderShopPanel(state) {
  return `
    ${state.ownsStarterPack ? '' : renderStarterPack()}
    ${renderAdOffer()}
    <div class="pack-grid">${FLOPPY_PACKS.map(renderPack).join('')}</div>
    <p class="fineprint">Prototype - no real payments are processed. Floppies have no cash value. Neither does your company. Yet.</p>`;
}

export function renderStore(activeTab, state) {
  return `
    <div class="app-store">
      <div class="store-banner">AppStore<small>The future of software. Today!</small></div>
      ${renderTabs(activeTab)}
      <div class="tab-panel">${activeTab === 'apps' ? renderAppsPanel(state) : renderShopPanel(state)}</div>
    </div>`;
}
