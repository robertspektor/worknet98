import { failure, success } from '../core/result.js';
import { findStoreApp } from '../data/storeApps.js';

export class StoreService {
  #state;

  constructor(state) {
    this.#state = state;
  }

  isInstalled(appId) {
    return this.#state.data.installedApps.includes(appId);
  }

  canAfford(app) {
    return this.#state.data[app.price.currency] >= app.price.amount;
  }

  install(appId) {
    const app = findStoreApp(appId);
    if (!app) return failure('This app does not exist. Yet.');
    if (this.isInstalled(appId)) return failure(`${app.name} is already installed.`);
    if (!this.canAfford(app)) return failure(app.price.currency === 'floppies' ? 'Not enough floppies.' : 'Not enough cash.');

    this.#state.update(state => {
      state[app.price.currency] -= app.price.amount;
      state.installedApps.push(appId);
    });

    return success(`${app.name} was installed. A shortcut is now on your desktop.`);
  }
}
