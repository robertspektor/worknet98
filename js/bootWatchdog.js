(() => {
  const TIMEOUT_MS = 8000;

  setTimeout(() => {
    if (document.documentElement.dataset.ready === 'true') return;

    const viewport = document.querySelector('[data-viewport]');
    viewport.innerHTML = '<div class="bios"><pre class="bios-text">SYSTEM FILES MISSING\n\nCorpOS could not be loaded.\nPlease reload the page.</pre></div>';
    document.querySelector('[data-power-led]').classList.add('is-error');
  }, TIMEOUT_MS);
})();
