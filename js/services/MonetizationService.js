import { formatCash } from '../core/money.js';
import { failure, success } from '../core/result.js';
import { GAME_CONFIG } from '../data/config.js';
import { findFloppyPack, STARTER_PACK } from '../data/offers.js';
import { baseIncomePerSecond } from './EconomyCalculator.js';

export class MonetizationService {
  #state;
  #gateway;
  #now;

  constructor(state, gateway, now = () => Date.now()) {
    this.#state = state;
    this.#gateway = gateway;
    this.#now = now;
  }

  async buyFloppyPack(packId) {
    const pack = findFloppyPack(packId);
    if (!pack) return failure('This offer does not exist.');
    if (!(await this.#gateway.checkout(pack))) return failure('Payment cancelled.');

    this.#state.update(state => { state.floppies += pack.floppies; });

    return success(`${pack.floppies} floppies were added to your account. Thank you, visionary!`);
  }

  async buyStarterPack() {
    if (this.#state.data.ownsStarterPack) return failure('You already own the Starter Pack.');
    if (!(await this.#gateway.checkout(STARTER_PACK))) return failure('Payment cancelled.');

    this.#state.update(state => {
      state.floppies += STARTER_PACK.floppies;
      state.ownsStarterPack = true;
    });

    return success('Starter Pack unlocked! Your employees suddenly feel 50% more motivated.');
  }

  adCooldownSeconds() {
    const readyAt = this.#state.data.lastAdAt + GAME_CONFIG.adCooldownSeconds * 1000;
    return Math.max(0, Math.ceil((readyAt - this.#now()) / 1000));
  }

  grantAdReward() {
    if (this.adCooldownSeconds() > 0) return failure('Please wait before watching the next ad.');

    const now = this.#now();
    this.#state.update(state => {
      state.floppies += GAME_CONFIG.adReward;
      state.boostUntil = now + GAME_CONFIG.adBoostSeconds * 1000;
      state.lastAdAt = now;
    });

    return success(`+${GAME_CONFIG.adReward} floppies and 2x income for ${GAME_CONFIG.adBoostSeconds} seconds!`);
  }

  timeWarp() {
    const { timeWarpCost, timeWarpSeconds } = GAME_CONFIG;
    const earned = baseIncomePerSecond(this.#state.data) * timeWarpSeconds;

    if (this.#state.data.floppies < timeWarpCost) return failure('Not enough floppies.');
    if (earned <= 0) return failure('Time is only money if someone works. Hire employees first.');

    this.#state.update(state => {
      state.floppies -= timeWarpCost;
      state.cash += earned;
    });

    return success(`You skipped ${timeWarpSeconds / 60} minutes and earned ${formatCash(earned)}.`);
  }
}
