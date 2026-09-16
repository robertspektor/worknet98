import assert from 'node:assert/strict';
import { describe, it } from 'node:test';
import { clickValue, hireCost, incomePerSecond } from '../js/services/EconomyCalculator.js';
import { EconomyService } from '../js/services/EconomyService.js';
import { createGame, lemonadeCompany } from './helpers.js';

const NOW = 1_000_000;

describe('EconomyService', () => {
  it('founds a company and pays the founding cost', () => {
    const { state } = createGame();
    const result = new EconomyService(state).foundCompany('  Garage Inc.  ', 'lemonade');

    assert.equal(result.ok, true);
    assert.equal(state.data.company.name, 'Garage Inc.');
    assert.equal(state.data.cash, 400);
  });

  it('refuses to found a company without enough cash', () => {
    const { state } = createGame({ cash: 50 });
    const result = new EconomyService(state).foundCompany('Broke Inc.', 'cat-pictures');

    assert.equal(result.ok, false);
    assert.equal(state.data.company, null);
  });

  it('earns cash per click and hires employees with growing cost', () => {
    const { state } = createGame({ company: lemonadeCompany, cash: 100 });
    const economy = new EconomyService(state, () => NOW);

    assert.equal(economy.work(), 2);
    assert.equal(economy.hire().ok, true);
    assert.equal(state.data.employees, 1);
    assert.equal(state.data.cash, 77);
    assert.equal(hireCost(state.data), 29);
  });

  it('pays passive income on tick', () => {
    const { state } = createGame({ company: lemonadeCompany, cash: 0, employees: 10 });
    new EconomyService(state, () => NOW).tick(1);

    assert.equal(state.data.cash, 10);
  });
});

describe('EconomyCalculator', () => {
  it('stacks installed apps, starter pack and ad boost', () => {
    const state = {
      company: lemonadeCompany,
      employees: 10,
      installedApps: ['spreadsheet', 'fax-blaster', 'autoclicker'],
      ownsStarterPack: true,
      boostUntil: NOW + 5_000,
    };

    assert.equal(clickValue(state, NOW), 8);
    assert.equal(incomePerSecond(state, NOW), (10 * 1.75 + 2 * 4) * 2);
  });
});
