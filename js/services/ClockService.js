import { GAME_CONFIG } from '../data/config.js';

export class ClockService {
  #state;
  #elapsedSeconds = 0;

  constructor(state) {
    this.#state = state;
  }

  tick(seconds) {
    this.#elapsedSeconds += seconds;
    if (this.#elapsedSeconds < GAME_CONFIG.secondsPerGameDay) return;

    this.#elapsedSeconds -= GAME_CONFIG.secondsPerGameDay;
    this.#state.update(state => { state.gameDay += 1; });
  }
}
