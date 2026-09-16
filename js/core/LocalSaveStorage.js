const STORAGE_KEY = 'recompany.save.v1';

export class LocalSaveStorage {
  load() {
    try {
      const raw = localStorage.getItem(STORAGE_KEY);
      return raw ? JSON.parse(raw) : null;
    } catch {
      return null;
    }
  }

  save(data) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
    } catch {
      // Saving is a convenience; the game keeps running without it.
    }
  }

  clear() {
    try {
      localStorage.removeItem(STORAGE_KEY);
    } catch {
      // Nothing to clear.
    }
  }
}
