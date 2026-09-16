import { EventBus } from './core/EventBus.js';
import { GameLoop } from './core/GameLoop.js';
import { GameState } from './core/GameState.js';
import { LocalSaveStorage } from './core/LocalSaveStorage.js';
import { MockPaymentGateway } from './payments/MockPaymentGateway.js';
import { ClockService } from './services/ClockService.js';
import { EconomyService } from './services/EconomyService.js';
import { GoalService } from './services/GoalService.js';
import { MailService } from './services/MailService.js';
import { MonetizationService } from './services/MonetizationService.js';
import { StoreService } from './services/StoreService.js';
import { UiTicker } from './services/UiTicker.js';
import { AdPlayer } from './ui/AdPlayer.js';
import { BootScreen } from './ui/BootScreen.js';
import { Computer } from './ui/Computer.js';
import { Desktop } from './ui/Desktop.js';
import { Dialog } from './ui/Dialog.js';
import { ScreenScaler } from './ui/ScreenScaler.js';
import { Sound } from './ui/Sound.js';

const screen = document.querySelector('[data-screen]');
const viewport = document.querySelector('[data-viewport]');
const powerButton = document.querySelector('[data-power-button]');
const led = document.querySelector('[data-power-led]');

const bus = new EventBus();
const state = new GameState(bus, new LocalSaveStorage());
const dialog = new Dialog();

const services = {
  bus,
  state,
  dialog,
  sound: new Sound(),
  adPlayer: new AdPlayer(dialog),
  economy: new EconomyService(state),
  monetization: new MonetizationService(state, new MockPaymentGateway(dialog)),
  store: new StoreService(state),
  mail: new MailService(state, bus),
};

const loop = new GameLoop([
  services.economy,
  new ClockService(state),
  services.mail,
  new GoalService(state, bus),
  new UiTicker(bus),
]);

const computer = new Computer({
  screen,
  viewport,
  led,
  powerButton,
  loop,
  sound: services.sound,
  bootScreen: new BootScreen(),
  createDesktop: () => new Desktop(services, { onShutDown: () => computer.shutDown() }),
});

new ScreenScaler(screen, viewport);
powerButton.addEventListener('click', () => computer.togglePower());
document.documentElement.dataset.ready = 'true';
