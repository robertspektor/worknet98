import { AppLauncher } from '../apps/AppLauncher.js';
import { formatCash } from '../core/money.js';
import { DesktopIcons } from './DesktopIcons.js';
import { createElement, escapeHtml } from './dom.js';
import { StartMenu } from './StartMenu.js';
import { Taskbar } from './Taskbar.js';
import { Toast } from './Toast.js';
import { WindowManager } from './WindowManager.js';

export class Desktop {
  #services;
  #onShutDown;
  #root = null;
  #windows = null;
  #launcher = null;
  #taskbar = null;
  #icons = null;
  #toast = null;
  #unsubscribers = [];

  constructor(services, { onShutDown }) {
    this.#services = services;
    this.#onShutDown = onShutDown;
  }

  mount(viewport) {
    this.#root = createElement('<div class="os"><div class="desktop-icons"></div><div class="window-layer"></div></div>');
    viewport.replaceChildren(this.#root);
    this.#services.dialog.attachTo(this.#root);

    this.#windows = new WindowManager(this.#root.querySelector('.window-layer'), () => this.#taskbar?.update());
    this.#launcher = new AppLauncher(this.#services, this.#windows);
    this.#toast = new Toast(this.#root);
    this.#icons = new DesktopIcons(this.#root.querySelector('.desktop-icons'), appId => this.#launcher.open(appId));
    const startMenu = new StartMenu(this.#root, {
      openApp: appId => this.#launcher.open(appId),
      resetGame: () => this.#resetGame(),
      shutDown: () => this.#onShutDown(),
    }, isVisible => this.#taskbar.setStartActive(isVisible));
    this.#taskbar = new Taskbar(this.#root, this.#services, this.#windows, {
      toggleStartMenu: () => startMenu.toggle(),
      openApp: (appId, options) => this.#launcher.open(appId, options),
    });

    this.#subscribe();
    this.#update();
  }

  unmount() {
    this.#unsubscribers.forEach(unsubscribe => unsubscribe());
    this.#unsubscribers = [];
    this.#windows.closeAll();
    this.#toast.hide();
    this.#services.dialog.detach();
    this.#root.remove();
  }

  #subscribe() {
    const { bus } = this.#services;
    this.#unsubscribers = [
      bus.on('state:changed', () => this.#update()),
      bus.on('ui:tick', () => this.#update()),
      bus.on('mail:received', mail => this.#announceMail(mail)),
      bus.on('app:open', ({ appId, options }) => this.#launcher.open(appId, options)),
      bus.on('game:won', () => this.#celebrateIpo()),
    ];
  }

  #update() {
    this.#windows.updateAll();
    this.#taskbar.update();
    this.#icons.update(this.#launcher.desktopEntries());
  }

  #announceMail(mail) {
    this.#services.sound.mail();
    this.#toast.show({
      title: 'You\'ve got mail!',
      message: `${mail.from}: ${mail.subject}`,
      onClick: () => this.#launcher.open('mail', { mailId: mail.id }),
    });
  }

  async #resetGame() {
    const confirmed = await this.#services.dialog.confirm('Reset Game', 'Delete your company, your cash and all of your dreams?', 'Reset');
    if (!confirmed) return;

    this.#windows.closeAll();
    this.#services.state.reset();
  }

  async #celebrateIpo() {
    const { sound, dialog, state } = this.#services;
    sound.fanfare();
    await dialog.show({
      title: 'IPO!!!',
      icon: 'logo',
      body: `
        <p class="dialog-headline">${escapeHtml(state.data.company?.name ?? 'Your company')} just went public!</p>
        <p>You turned $500 from Uncle Gerald into ${formatCash(state.data.cash)}. The stock is called $RETRO and nobody knows why it is going up.</p>
        <p>Thanks for playing the prototype!</p>`,
      buttons: [{ label: 'Ring the bell', value: true, primary: true }],
    });
  }
}
