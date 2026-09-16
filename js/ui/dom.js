const HTML_ENTITIES = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };

export function escapeHtml(value) {
  return String(value).replace(/[&<>"']/g, character => HTML_ENTITIES[character]);
}

export function createElement(html) {
  const template = document.createElement('template');
  template.innerHTML = html.trim();
  return template.content.firstElementChild;
}

export function wait(milliseconds) {
  return new Promise(resolve => setTimeout(resolve, milliseconds));
}

export function renderScale(element) {
  return element.getBoundingClientRect().width / element.offsetWidth || 1;
}
