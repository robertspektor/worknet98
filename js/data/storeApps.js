export const STORE_APPS = [
  {
    id: 'spreadsheet',
    name: 'SpreadSheet 3.11',
    description: 'Now with 256 rows! Employees look busy and actually are.',
    price: { currency: 'cash', amount: 300 },
    effect: { incomeBonus: 0.25 },
  },
  {
    id: 'fax-blaster',
    name: 'Fax Blaster Pro',
    description: 'Spam customers the old-fashioned way.',
    price: { currency: 'cash', amount: 750 },
    effect: { clickMultiplier: 2 },
  },
  {
    id: 'autoclicker',
    name: 'AutoClicker 98',
    description: 'Clicks so you do not have to. Voids warranty.',
    price: { currency: 'cash', amount: 2_000 },
    effect: { autoClicksPerSecond: 2 },
  },
  {
    id: 'y2k-insurance',
    name: 'Y2K Panic Insurance',
    description: 'Nobody knows what it does. Everybody buys it.',
    price: { currency: 'cash', amount: 8_000 },
    effect: { incomeBonus: 0.5 },
  },
  {
    id: 'staply',
    name: 'Staply Business Advisor',
    description: 'It looks like you are trying to get rich. Need help?',
    price: { currency: 'floppies', amount: 15 },
    effect: { incomeBonus: 1 },
  },
  {
    id: 'turbo-modem',
    name: 'Turbo Modem 56k',
    description: 'BEEEP-KRRRR-SHHHH. Now every click goes further.',
    price: { currency: 'floppies', amount: 25 },
    effect: { clickMultiplier: 3 },
  },
  {
    id: 'bitmine',
    name: 'BitMine 1997',
    description: 'Mines a currency that does not exist yet.',
    price: { currency: 'floppies', amount: 40 },
    effect: { autoClicksPerSecond: 5 },
  },
];

export function findStoreApp(id) {
  return STORE_APPS.find(app => app.id === id) ?? null;
}
