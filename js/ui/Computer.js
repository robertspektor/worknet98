export class Computer {
  #parts;
  #isOn = false;
  #session = 0;
  #desktop = null;

  constructor(parts) {
    this.#parts = parts;
  }

  togglePower() {
    if (this.#isOn) {
      this.powerOff();
    } else {
      this.powerOn();
    }
  }

  async powerOn() {
    const { viewport, powerButton, bootScreen, sound, loop, createDesktop } = this.#parts;
    const session = this.#startSession(true);
    sound.unlock();
    powerButton.classList.remove('is-hinting');

    const booted = await bootScreen.run(viewport, () => session === this.#session);
    if (!booted) return;

    this.#desktop = createDesktop();
    this.#desktop.mount(viewport);
    loop.start();
    sound.startup();
  }

  powerOff() {
    this.#startSession(false);
  }

  shutDown() {
    this.#teardownDesktop();
    this.#parts.viewport.innerHTML = `
      <div class="safe-to-turn-off">
        It's now safe to turn off<br>your computer.
        <small>Your company will keep existing. Probably.</small>
      </div>`;
  }

  #startSession(isOn) {
    const { screen, viewport, led } = this.#parts;
    this.#session += 1;
    this.#isOn = isOn;
    this.#teardownDesktop();
    viewport.replaceChildren();
    led.classList.toggle('is-on', isOn);
    screen.classList.toggle('is-on', isOn);

    return this.#session;
  }

  #teardownDesktop() {
    this.#parts.loop.stop();
    this.#desktop?.unmount();
    this.#desktop = null;
  }
}
