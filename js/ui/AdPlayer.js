import { GAME_CONFIG } from '../data/config.js';
import { escapeHtml } from './dom.js';

const ADS = [
  { headline: 'SUPER MODEM 2400', copy: 'Surf the Information Superhighway at a blazing 2400 baud!', cta: 'CALL 1-800-DIAL-UP' },
  { headline: 'MEGA PAGER 3000', copy: 'Be reachable. Everywhere. Forever. Sorry.', cta: 'BEEP BEEP NOW!' },
  { headline: 'NEON FANNY PACKS', copy: 'Store your floppies in style. Available in 12 radioactive colors.', cta: 'ONLY $19.95' },
];

export class AdPlayer {
  #dialog;

  constructor(dialog) {
    this.#dialog = dialog;
  }

  async play() {
    const ad = ADS[Math.floor(Math.random() * ADS.length)];
    const completed = await this.#dialog.show({
      title: 'A word from our sponsors',
      body: this.#template(ad),
      buttons: [
        { label: 'Collect reward', value: true, primary: true, disabled: true },
        { label: 'Close', value: false },
      ],
      onOpen: overlay => this.#startCountdown(overlay),
    });

    return Boolean(completed);
  }

  #startCountdown(overlay) {
    let remaining = GAME_CONFIG.adDurationSeconds;
    const timerLabel = overlay.querySelector('.ad-timer');
    const rewardButton = overlay.querySelector('.button-primary');

    const timer = setInterval(() => {
      remaining -= 1;
      timerLabel.textContent = `Reward in ${remaining}s`;
      if (remaining > 0 && overlay.isConnected) return;

      clearInterval(timer);
      timerLabel.textContent = 'Thanks for watching!';
      rewardButton.disabled = false;
      rewardButton.focus();
    }, 1000);
  }

  #template(ad) {
    return `
      <div class="ad">
        <div class="ad-screen">
          <div class="ad-headline">${escapeHtml(ad.headline)}</div>
          <div class="ad-copy">${escapeHtml(ad.copy)}</div>
          <div class="ad-cta">${escapeHtml(ad.cta)}</div>
        </div>
        <div class="ad-timer">Reward in ${GAME_CONFIG.adDurationSeconds}s</div>
      </div>`;
  }
}
