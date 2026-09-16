export class GameLoop {
  #tickables;
  #intervalMs;
  #timer = null;

  constructor(tickables, intervalMs = 1000) {
    this.#tickables = tickables;
    this.#intervalMs = intervalMs;
  }

  start() {
    if (this.#timer) return;
    const seconds = this.#intervalMs / 1000;
    this.#timer = setInterval(() => this.#tickables.forEach(tickable => tickable.tick(seconds)), this.#intervalMs);
  }

  stop() {
    clearInterval(this.#timer);
    this.#timer = null;
  }
}
