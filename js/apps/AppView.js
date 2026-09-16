export class AppView {
  #renderedKey = null;

  constructor(services) {
    this.services = services;
    this.body = null;
  }

  get data() {
    return this.services.state.data;
  }

  mount(body) {
    this.body = body;
    body.addEventListener('click', event => this.#dispatch(event));
    this.update();
  }

  update() {
    if (!this.body) return;

    const key = this.structureKey();
    if (key !== this.#renderedKey) {
      this.#renderedKey = key;
      this.body.innerHTML = this.render();
    }
    this.refresh();
  }

  structureKey() {
    return 'static';
  }

  render() {
    return '';
  }

  refresh() {}

  handleAction() {}

  receiveOptions() {}

  destroy() {}

  bindText(name, text) {
    this.body.querySelectorAll(`[data-bind="${name}"]`).forEach(element => {
      if (element.textContent !== text) element.textContent = text;
    });
  }

  setDisabled(action, isDisabled) {
    this.body.querySelectorAll(`[data-action="${action}"]`).forEach(element => { element.disabled = isDisabled; });
  }

  #dispatch(event) {
    const target = event.target.closest('[data-action]');
    if (!target || target.disabled || !this.body.contains(target)) return;

    this.services.sound.click();
    this.handleAction(target.dataset.action, target, event);
  }
}
