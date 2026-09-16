export class ScreenScaler {
  #screen;
  #viewport;

  constructor(screen, viewport) {
    this.#screen = screen;
    this.#viewport = viewport;
    new ResizeObserver(() => this.#apply()).observe(screen);
    this.#apply();
  }

  #apply() {
    const scale = this.#screen.clientWidth / this.#viewport.offsetWidth;
    this.#viewport.style.setProperty('--scale', String(scale));
  }
}
