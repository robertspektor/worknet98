import { GAME_CONFIG } from '../data/config.js';
import { findIndustry } from '../data/industries.js';
import { STARTER_PACK } from '../data/offers.js';
import { findStoreApp } from '../data/storeApps.js';

const NEUTRAL_MODIFIERS = Object.freeze({ incomeBonus: 0, clickMultiplier: 1, autoClicksPerSecond: 0 });

export function collectModifiers(state) {
  const effects = state.installedApps.map(id => findStoreApp(id)?.effect).filter(Boolean);
  if (state.ownsStarterPack) effects.push(STARTER_PACK.effect);

  return effects.reduce((total, effect) => ({
    incomeBonus: total.incomeBonus + (effect.incomeBonus ?? 0),
    clickMultiplier: total.clickMultiplier * (effect.clickMultiplier ?? 1),
    autoClicksPerSecond: total.autoClicksPerSecond + (effect.autoClicksPerSecond ?? 0),
  }), NEUTRAL_MODIFIERS);
}

export function boostSecondsLeft(state, now) {
  return Math.max(0, Math.ceil((state.boostUntil - now) / 1000));
}

function boostMultiplier(state, now) {
  return boostSecondsLeft(state, now) > 0 ? GAME_CONFIG.adBoostMultiplier : 1;
}

function baseClickValue(state) {
  const industry = findIndustry(state.company?.industryId);
  return industry ? industry.clickValue * collectModifiers(state).clickMultiplier : 0;
}

export function clickValue(state, now) {
  return baseClickValue(state) * boostMultiplier(state, now);
}

export function baseIncomePerSecond(state) {
  const industry = findIndustry(state.company?.industryId);
  if (!industry) return 0;

  const modifiers = collectModifiers(state);
  const staffIncome = state.employees * industry.employeeIncome * (1 + modifiers.incomeBonus);
  const autoClickIncome = modifiers.autoClicksPerSecond * baseClickValue(state);

  return staffIncome + autoClickIncome;
}

export function incomePerSecond(state, now) {
  return baseIncomePerSecond(state) * boostMultiplier(state, now);
}

export function hireCost(state) {
  return Math.round(GAME_CONFIG.hireBaseCost * GAME_CONFIG.hireCostGrowth ** state.employees);
}

export function goalProgress(state) {
  return Math.min(1, state.cash / GAME_CONFIG.ipoGoal);
}
