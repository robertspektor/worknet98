import { EventBus } from '../js/core/EventBus.js';
import { GameState } from '../js/core/GameState.js';

export class MemoryStorage {
  saved = null;

  load() {
    return this.saved;
  }

  save(data) {
    this.saved = structuredClone(data);
  }

  clear() {
    this.saved = null;
  }
}

export function createGame(overrides = {}) {
  const bus = new EventBus();
  const storage = new MemoryStorage();
  storage.saved = overrides;
  const state = new GameState(bus, storage);

  return { bus, storage, state };
}

export const lemonadeCompany = { name: 'Test Corp', industryId: 'lemonade', foundedDay: 0 };
