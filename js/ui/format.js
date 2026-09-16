import { formatCash } from '../core/money.js';
import { iconSvg } from './icons.js';

const GAME_EPOCH = Date.UTC(1995, 0, 2);
const DAY_IN_MS = 86_400_000;

export function formatDate(gameDay) {
  return new Date(GAME_EPOCH + gameDay * DAY_IN_MS).toLocaleDateString('en-US', {
    month: 'short',
    day: '2-digit',
    year: 'numeric',
    timeZone: 'UTC',
  });
}

export function formatEffect(effect) {
  const parts = [];
  if (effect.incomeBonus) parts.push(`+${Math.round(effect.incomeBonus * 100)}% staff income`);
  if (effect.clickMultiplier) parts.push(`x${effect.clickMultiplier} cash per click`);
  if (effect.autoClicksPerSecond) parts.push(`${effect.autoClicksPerSecond} auto-clicks per second`);

  return parts.join(', ');
}

export function floppyAmountHtml(amount, iconSize = 14) {
  return `<span class="floppy-amount">${iconSvg('floppy', iconSize)}<span>${amount}</span></span>`;
}

export function priceHtml(price) {
  return price.currency === 'floppies' ? floppyAmountHtml(price.amount) : formatCash(price.amount);
}
