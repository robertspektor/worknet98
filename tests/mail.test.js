import assert from 'node:assert/strict';
import { describe, it } from 'node:test';
import { MailService } from '../js/services/MailService.js';
import { createGame, lemonadeCompany } from './helpers.js';

describe('MailService', () => {
  it('delivers each triggered mail exactly once', () => {
    const { state, bus } = createGame();
    const received = [];
    bus.on('mail:received', mail => received.push(mail.id));
    const mail = new MailService(state, bus);

    mail.tick();
    mail.tick();

    assert.deepEqual(received, ['welcome']);
    assert.equal(mail.unreadCount(), 1);
  });

  it('lets the prince take your money only once', () => {
    const { state, bus } = createGame({ company: lemonadeCompany, cash: 1_000 });
    const mail = new MailService(state, bus);
    mail.tick();

    assert.equal(mail.runAction('prince').ok, true);
    assert.equal(mail.runAction('prince').ok, false);
    assert.equal(state.data.cash, 800);
  });
});
