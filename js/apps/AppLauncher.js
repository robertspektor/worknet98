import { findStoreApp } from '../data/storeApps.js';
import { CompanyApp } from './company/CompanyApp.js';
import { InstalledApp } from './InstalledApp.js';
import { MailApp } from './mail/MailApp.js';
import { RecycleBinApp } from './RecycleBinApp.js';
import { AppStoreApp } from './store/AppStoreApp.js';

const BUILTIN_APPS = [
  { id: 'company', title: 'My Company', icon: 'briefcase', width: 470, height: 420, createView: services => new CompanyApp(services) },
  { id: 'appstore', title: 'AppStore', icon: 'store', width: 540, height: 500, createView: services => new AppStoreApp(services) },
  { id: 'mail', title: 'Mail', icon: 'mail', width: 560, height: 430, createView: services => new MailApp(services) },
  { id: 'recycle', title: 'Recycle Bin', icon: 'trash', width: 380, height: 290, createView: services => new RecycleBinApp(services) },
];

export class AppLauncher {
  #services;
  #windows;

  constructor(services, windows) {
    this.#services = services;
    this.#windows = windows;
  }

  desktopEntries() {
    const installedApps = this.#services.state.data.installedApps
      .map(findStoreApp)
      .filter(Boolean)
      .map(app => this.#installedAppEntry(app));

    return [...BUILTIN_APPS, ...installedApps];
  }

  open(appId, options = {}) {
    const entry = this.desktopEntries().find(candidate => candidate.id === appId);
    if (!entry) return;

    this.#windows.open(entry, () => entry.createView(this.#services), options);
  }

  #installedAppEntry(app) {
    return {
      id: app.id,
      title: app.name,
      icon: 'program',
      width: 340,
      height: 250,
      createView: services => new InstalledApp(services, app),
    };
  }
}
