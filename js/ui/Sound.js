export class Sound {
  #context = null;

  unlock() {
    const AudioContextClass = window.AudioContext ?? window.webkitAudioContext;
    if (!AudioContextClass) return;

    this.#context ??= new AudioContextClass();
    if (this.#context.state === 'suspended') this.#context.resume();
  }

  click() {
    this.#play([[1400, 0, 0.02]], 'square', 0.015);
  }

  cash() {
    this.#play([[1319, 0, 0.05], [1760, 0.05, 0.1]], 'square', 0.025);
  }

  mail() {
    this.#play([[880, 0, 0.12], [1175, 0.12, 0.25]], 'sine', 0.08);
  }

  error() {
    this.#play([[160, 0, 0.25]], 'sawtooth', 0.04);
  }

  startup() {
    this.#play([[523, 0, 0.3], [659, 0.15, 0.3], [784, 0.3, 0.3], [1047, 0.45, 0.8]], 'triangle', 0.08);
  }

  fanfare() {
    this.#play([[523, 0, 0.14], [523, 0.15, 0.14], [523, 0.3, 0.14], [698, 0.45, 0.7]], 'square', 0.04);
  }

  #play(notes, type, volume) {
    if (!this.#context) return;
    const start = this.#context.currentTime;
    notes.forEach(([frequency, offset, duration]) => this.#tone(frequency, start + offset, duration, type, volume));
  }

  #tone(frequency, startAt, duration, type, volume) {
    const oscillator = this.#context.createOscillator();
    const gain = this.#context.createGain();

    oscillator.type = type;
    oscillator.frequency.value = frequency;
    gain.gain.setValueAtTime(volume, startAt);
    gain.gain.exponentialRampToValueAtTime(0.0001, startAt + duration);

    oscillator.connect(gain).connect(this.#context.destination);
    oscillator.start(startAt);
    oscillator.stop(startAt + duration);
  }
}
