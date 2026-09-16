import { GAME_CONFIG } from '../data/config.js';

export class GoalService {
  #state;
  #bus;

  constructor(state, bus) {
    this.#state = state;
    this.#bus = bus;
  }

  tick() {
    const { hasWon, cash } = this.#state.data;
    if (hasWon || cash < GAME_CONFIG.ipoGoal) return;

    this.#state.update(state => { state.hasWon = true; });
    this.#bus.emit('game:won');
  }
}
