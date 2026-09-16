import { findStoreApp } from '../../data/storeApps.js';
import { AppView } from '../AppView.js';
import { renderStore } from './storeTemplates.js';

export class AppStoreApp extends AppView {
  #tab = 'apps';

  receiveOptions({ tab } = {}) {
    if (!tab) return;
    this.#tab = tab;
    this.update();
  }

  structureKey() {
    return `${this.#tab}|${this.data.installedApps.join(',')}|${this.data.ownsStarterPack}`;
  }

  render() {
    return renderStore(this.#tab, this.data);
  }

  refresh() {
    if (this.#tab === 'apps') {
      this.#refreshInstallButtons();
    } else {
      this.#refreshAdButton();
    }
  }

  handleAction(action, element) {
    const handlers = {
      tab: () => this.receiveOptions({ tab: element.dataset.tab }),
      install: () => this.#install(element.dataset.appId),
      pack: () => this.#announce(this.services.monetization.buyFloppyPack(element.dataset.packId)),
      starter: () => this.#announce(this.services.monetization.buyStarterPack()),
      ad: () => this.#watchAd(),
    };
    handlers[action]?.();
  }

  #refreshInstallButtons() {
    this.body.querySelectorAll('[data-action="install"]').forEach(button => {
      const app = findStoreApp(button.dataset.appId);
      button.disabled = app.price.currency === 'cash' && !this.services.store.canAfford(app);
    });
  }

  #refreshAdButton() {
    const cooldown = this.services.monetization.adCooldownSeconds();
    this.bindText('adLabel', cooldown > 0 ? `Next ad in ${cooldown}s` : 'Watch ad');
    this.setDisabled('ad', cooldown > 0);
  }

  async #install(appId) {
    const { store, sound, dialog } = this.services;
    const app = findStoreApp(appId);
    if (app.price.currency === 'floppies' && !store.canAfford(app)) {
      await this.#offerFloppyShop(app);
      return;
    }

    const result = store.install(appId);
    if (result.ok) {
      sound.cash();
      await dialog.alert('Installation complete', result.message);
      return;
    }
    sound.error();
    await dialog.alert('Installation failed', result.message, 'warning');
  }

  async #offerFloppyShop(app) {
    const message = `${app.name} needs ${app.price.amount} floppies, but you only have ${this.data.floppies}. Visit the Floppy Shop?`;
    const wantsFloppies = await this.services.dialog.confirm('Not enough floppies', message, 'Get floppies', 'floppy');
    if (wantsFloppies) this.receiveOptions({ tab: 'shop' });
  }

  async #watchAd() {
    const completed = await this.services.adPlayer.play();
    if (!completed) return;
    await this.#announce(Promise.resolve(this.services.monetization.grantAdReward()));
  }

  async #announce(pendingResult) {
    const result = await pendingResult;
    if (!result.ok) return;

    this.services.sound.cash();
    await this.services.dialog.alert('Thank you!', result.message, 'floppy');
  }
}
