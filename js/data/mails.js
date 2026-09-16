import { GAME_CONFIG } from './config.js';

export const MAILS = [
  {
    id: 'welcome',
    from: 'Uncle Gerald',
    subject: 'Your startup money',
    trigger: () => true,
    body: [
      'Hey kiddo,',
      'I wired you $500. Do not spend it all on Beanie Babies.',
      'Open "My Company" on the desktop and start something big. Your cousin Kevin sells lemonade on the internet now. If Kevin can do it, so can you.',
      'Love, Uncle Gerald',
      'P.S. Is this e-mail thing working? HELLO??',
    ],
  },
  {
    id: 'appstore-tip',
    from: 'CorpOS AppStore',
    subject: 'Productivity is one download away!',
    trigger: state => state.company !== null,
    body: [
      'Congratulations on founding your company!',
      'Real CEOs do not work harder. They install software. Visit the AppStore for tools that make your employees up to 100% more productive.',
      'Some apps require floppies. Floppies are the currency of the future.',
    ],
    action: { type: 'openApp', label: 'Open AppStore', appId: 'appstore' },
  },
  {
    id: 'prince',
    from: 'Prince Adewale III',
    subject: 'URGENT BUSINESS PROPOSAL!!!',
    trigger: state => state.company !== null && state.cash >= 800,
    body: [
      'DEAREST FRIEND,',
      'I AM A PRINCE WITH $10,000,000 STUCK IN A BANK. I CHOSE YOU BECAUSE YOU SEEM VERY TRUSTWORTHY AND ALSO HAVE A MODEM.',
      'KINDLY SEND A SMALL PROCESSING FEE OF $200 AND I WILL SHARE THE FORTUNE WITH YOU.',
      'GOD BLESS.',
    ],
    action: {
      type: 'scam',
      label: 'Send $200 processing fee',
      amount: 200,
      result: 'Your $200 is on its way. The prince will surely get back to you any day now. Any day.',
    },
  },
  {
    id: 'starter-offer',
    from: 'AppStore Deals',
    subject: 'Exclusive: your personal Starter Pack',
    trigger: state => state.company !== null && state.gameDay >= 3 && !state.ownsStarterPack,
    body: [
      'Dear valued CEO,',
      'Our computers have calculated that you are exactly the kind of visionary who deserves the Starter Pack: 60 floppies and +50% staff income. Forever.',
      'Available only once. Like youth.',
    ],
    action: { type: 'openApp', label: 'Show me the deal', appId: 'appstore', options: { tab: 'shop' } },
  },
  {
    id: 'venture-capital',
    from: 'Chad @ Synergy Ventures',
    subject: 'Let\'s disrupt something, bro',
    trigger: state => state.employees >= 5,
    body: [
      'Yo,',
      'Saw your company on a web ring. Huge vibes. We want to throw $2,000 at you. No due diligence, no questions, no idea what you do.',
      'Let\'s circle back and move the needle.',
      'Chad',
    ],
    action: {
      type: 'gift',
      label: 'Accept $2,000 seed round',
      amount: 2_000,
      result: 'Chad wired $2,000 and a high-five emoji your computer cannot display.',
    },
  },
  {
    id: 'y2k',
    from: 'The IT Guy',
    subject: 'Y2K is coming. Nobody is safe.',
    trigger: state => state.cash >= 20_000,
    body: [
      'Hi,',
      'When the clock hits 2000, all computers will think it is 1900. Your employees will be paid in horses.',
      'I strongly recommend Y2K Panic Insurance from the AppStore. I do not know what it does. Neither does anyone.',
    ],
    action: { type: 'openApp', label: 'Open AppStore', appId: 'appstore' },
  },
  {
    id: 'ipo-rumors',
    from: 'The Daily Ticker',
    subject: 'Rumors of an IPO',
    trigger: state => state.cash >= GAME_CONFIG.ipoGoal / 2,
    body: [
      'Sources close to the company (your cat) report that an IPO could happen soon.',
      'Analysts describe the business model as "unclear, but on the internet".',
    ],
  },
];

export function findMail(id) {
  return MAILS.find(mail => mail.id === id) ?? null;
}
