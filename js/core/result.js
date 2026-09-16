export const success = (message = '') => ({ ok: true, message });

export const failure = message => ({ ok: false, message });
