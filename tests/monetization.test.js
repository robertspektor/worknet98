import assert from 'node:assert/strict';
import { describe, it } from 'node:test';
import { GAME_CONFIG } from '../js/data/config.js';
import { MonetizationService } from '../js/services/MonetizationService.js';
import { StoreService } from '../js/services/StoreService.js';
import { createGame, lemonadeCompany } from './helpers.js';

const NOW = 5_000_000;
const approvingGateway = { checkout: async () => true };
const decliningGateway = { checkout: async () => false };

describe('MonetizationService', () => {
  it('adds floppies only after a successful checkout', async () => {
    const { state } = createGame({ floppies: 0 });

    assert.equal((await new MonetizationService(state, decliningGateway).buyFloppyPack('box')).ok, false);
    assert.equal(state.data.floppies, 0);

    assert.equal((await new MonetizationService(state, approvingGateway).buyFloppyPack('box')).ok, true);
    assert.equal(state.data.floppies, 120);
  });

  it('sells the starter pack only once', async () => {
    const { state } = createGame({ floppies: 0 });
    const monetization = new MonetizationService(state, approvingGateway);

    assert.equal((await monetization.buyStarterPack()).ok, true);
    assert.equal((await monetization.buyStarterPack()).ok, false);
    assert.equal(state.data.floppies, 60);
  });

  it('rewards ads and enforces the cooldown', () => {
    let now = NOW;
    const { state } = createGame({ floppies: 0 });
    const monetization = new MonetizationService(state, approvingGateway, () => now);

    assert.equal(monetization.grantAdReward().ok, true);
    assert.equal(monetization.grantAdReward().ok, false);
    assert.equal(state.data.floppies, GAME_CONFIG.adReward);

    now += GAME_CONFIG.adCooldownSeconds * 1000;
    assert.equal(monetization.adCooldownSeconds(), 0);
  });

  it('converts floppies into skipped income', () => {
    const { state } = createGame({ company: lemonadeCompany, employees: 2, cash: 0, floppies: 10 });
    const result = new MonetizationService(state, approvingGateway, () => NOW).timeWarp();

    assert.equal(result.ok, true);
    assert.equal(state.data.floppies, 0);
    assert.equal(state.data.cash, 2 * GAME_CONFIG.timeWarpSeconds);
  });
});

describe('StoreService', () => {
  it('installs apps with the matching currency', () => {
    const { state } = createGame({ cash: 300, floppies: 14 });
    const store = new StoreService(state);

    assert.equal(store.install('spreadsheet').ok, true);
    assert.equal(store.install('staply').ok, false);
    assert.deepEqual(state.data.installedApps, ['spreadsheet']);
    assert.equal(state.data.cash, 0);
  });
});
