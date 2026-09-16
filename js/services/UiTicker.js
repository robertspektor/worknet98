export class UiTicker {
  #bus;

  constructor(bus) {
    this.#bus = bus;
  }

  tick() {
    this.#bus.emit('ui:tick');
  }
}
