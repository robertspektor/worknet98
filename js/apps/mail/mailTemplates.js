import { findMail } from '../../data/mails.js';
import { escapeHtml } from '../../ui/dom.js';
import { formatDate } from '../../ui/format.js';
import { iconSvg } from '../../ui/icons.js';

function renderRow(entry, selectedId) {
  const mail = findMail(entry.id);
  const classes = ['mail-row', entry.read ? '' : 'is-unread', entry.id === selectedId ? 'is-selected' : ''].join(' ');

  return `
    <button class="${classes}" data-action="select" data-mail-id="${entry.id}">
      ${iconSvg('mail', 16)}
      <span>${escapeHtml(mail.from)}</span>
      <span>${escapeHtml(mail.subject)}</span>
      <span>${formatDate(entry.receivedDay)}</span>
    </button>`;
}

function renderReader(entry) {
  if (!entry) return '<p class="mail-empty">Select a message to read it.</p>';

  const mail = findMail(entry.id);
  const action = mail.action && !entry.actionDone
    ? `<div class="app-actions"><button class="button button-primary" data-action="runMailAction">${escapeHtml(mail.action.label)}</button></div>`
    : '';

  return `
    <div class="mail-meta">
      <div><b>From:</b> ${escapeHtml(mail.from)}</div>
      <div><b>Subject:</b> ${escapeHtml(mail.subject)}</div>
    </div>
    <div class="mail-body">${mail.body.map(paragraph => `<p>${escapeHtml(paragraph)}</p>`).join('')}</div>
    ${action}`;
}

export function renderMail(inbox, selectedId) {
  const newestFirst = [...inbox].reverse();

  return `
    <div class="mail-app">
      <div class="mail-list sunken">
        <div class="mail-row mail-row-header"><span></span><span>From</span><span>Subject</span><span>Received</span></div>
        ${newestFirst.map(entry => renderRow(entry, selectedId)).join('')}
      </div>
      <div class="mail-reader sunken">${renderReader(inbox.find(entry => entry.id === selectedId))}</div>
    </div>`;
}
