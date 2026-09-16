export const INDUSTRIES = [
  {
    id: 'lemonade',
    name: 'Lemonade.com',
    tagline: 'Sell lemonade. On the INTERNET.',
    foundingCost: 100,
    clickValue: 2,
    employeeIncome: 1,
  },
  {
    id: 'floppy-recycling',
    name: 'Floppy Recycling Ltd.',
    tagline: 'Somebody has to do it.',
    foundingCost: 250,
    clickValue: 4,
    employeeIncome: 2,
  },
  {
    id: 'cat-pictures',
    name: 'CatPics Online',
    tagline: 'The true purpose of the internet.',
    foundingCost: 450,
    clickValue: 7,
    employeeIncome: 3,
  },
];

export function findIndustry(id) {
  return INDUSTRIES.find(industry => industry.id === id) ?? null;
}
