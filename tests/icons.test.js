import assert from 'node:assert/strict';
import { describe, it } from 'node:test';
import { ICONS, PALETTE } from '../js/ui/icons.js';

describe('pixel icons', () => {
  for (const [name, rows] of Object.entries(ICONS)) {
    it(`${name} is a valid 16x16 grid`, () => {
      assert.equal(rows.length, 16);
      rows.forEach((row, index) => {
        assert.equal(row.length, 16, `row ${index} of ${name}`);
        [...row].forEach(color => assert.ok(color === '.' || color in PALETTE, `unknown color "${color}" in ${name}`));
      });
    });
  }
});
