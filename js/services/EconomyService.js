import { failure, success } from '../core/result.js';
import { findIndustry } from '../data/industries.js';
import { clickValue, hireCost, incomePerSecond } from './EconomyCalculator.js';

const MAX_COMPANY_NAME_LENGTH = 32;

export class EconomyService {
  #state;
  #now;

  constructor(state, now = () => Date.now()) {
    this.#state = state;
    this.#now = now;
  }

  foundCompany(name, industryId) {
    const industry = findIndustry(industryId);
    const companyName = String(name ?? '').trim().slice(0, MAX_COMPANY_NAME_LENGTH);

    if (!industry || !companyName) return failure('Every empire needs a name and an industry.');
    if (this.#state.data.company) return failure('You already run a company. One empire at a time.');
    if (this.#state.data.cash < industry.foundingCost) return failure('Not enough cash. Maybe ask Uncle Gerald again?');

    this.#state.update(state => {
      state.cash -= industry.foundingCost;
      state.company = { name: companyName, industryId, foundedDay: state.gameDay };
    });

    return success(`${companyName} is now officially a company!`);
  }

  work() {
    const earned = clickValue(this.#state.data, this.#now());
    if (earned > 0) this.#state.update(state => { state.cash += earned; });

    return earned;
  }

  hire() {
    const cost = hireCost(this.#state.data);
    if (!this.#state.data.company) return failure('Found a company before hiring people.');
    if (this.#state.data.cash < cost) return failure('Not enough cash to hire anyone.');

    this.#state.update(state => {
      state.cash -= cost;
      state.employees += 1;
    });

    return success();
  }

  tick(seconds) {
    const earned = incomePerSecond(this.#state.data, this.#now()) * seconds;
    if (earned > 0) this.#state.update(state => { state.cash += earned; });
  }
}
