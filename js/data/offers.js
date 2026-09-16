export const FLOPPY_PACKS = [
  { id: 'handful', name: 'Handful of Floppies', floppies: 20, priceCents: 99, iconCount: 1, badge: null },
  { id: 'box', name: 'Box of Floppies', floppies: 120, priceCents: 499, iconCount: 2, badge: 'MOST POPULAR' },
  { id: 'crate', name: 'CEO Crate', floppies: 500, priceCents: 1499, iconCount: 3, badge: 'BEST VALUE' },
];

export const STARTER_PACK = Object.freeze({
  id: 'starter',
  name: 'Starter Pack',
  floppies: 60,
  priceCents: 299,
  effect: { incomeBonus: 0.5 },
});

export function findFloppyPack(id) {
  return FLOPPY_PACKS.find(pack => pack.id === id) ?? null;
}
