export function formatCash(amount) {
  return `$${Math.floor(amount).toLocaleString('en-US')}`;
}

export function formatRate(amountPerSecond) {
  return `$${amountPerSecond.toLocaleString('en-US', { maximumFractionDigits: 1 })}/s`;
}

export function formatEuro(cents) {
  return `€${(cents / 100).toFixed(2)}`;
}
