import { GAME_CONFIG } from '../data/config.js';

export function createInitialState() {
  return {
    cash: GAME_CONFIG.startingCash,
    floppies: GAME_CONFIG.startingFloppies,
    company: null,
    employees: 0,
    installedApps: [],
    inbox: [],
    gameDay: 0,
    boostUntil: 0,
    lastAdAt: 0,
    ownsStarterPack: false,
    hasWon: false,
  };
}

export class GameState {
  #bus;
  #storage;
  #data;

  constructor(bus, storage) {
    this.#bus = bus;
    this.#storage = storage;
    this.#data = { ...createInitialState(), ...storage.load() };
  }

  get data() {
    return this.#data;
  }

  update(mutate) {
    mutate(this.#data);
    this.#storage.save(this.#data);
    this.#bus.emit('state:changed', this.#data);
  }

  reset() {
    this.#data = createInitialState();
    this.#storage.clear();
    this.#bus.emit('state:changed', this.#data);
  }
}
