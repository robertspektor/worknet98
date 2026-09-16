import { describe, expect, it } from 'vite-plus/test';
import { driveReducer, emptyDrive, loadedDiskId } from './drive-state';

describe('driveReducer', () => {
    it('inserts a disk into an empty drive and loads it once settled', () => {
        const inserting = driveReducer(emptyDrive, {
            type: 'insert',
            diskId: 7,
        });
        const loaded = driveReducer(inserting, { type: 'settle' });

        expect(inserting).toEqual({ phase: 'inserting', diskId: 7 });
        expect(loadedDiskId(inserting)).toBeNull();
        expect(loaded).toEqual({ phase: 'loaded', diskId: 7 });
        expect(loadedDiskId(loaded)).toBe(7);
    });

    it('does not accept a second disk', () => {
        const loaded = { phase: 'loaded', diskId: 7 } as const;

        expect(driveReducer(loaded, { type: 'insert', diskId: 8 })).toBe(
            loaded,
        );
    });

    it('ejects a loaded disk and empties the drive once settled', () => {
        const ejecting = driveReducer(
            { phase: 'loaded', diskId: 7 },
            { type: 'eject' },
        );

        expect(ejecting).toEqual({ phase: 'ejecting', diskId: 7 });
        expect(driveReducer(ejecting, { type: 'settle' })).toEqual(emptyDrive);
    });

    it('ignores eject while the disk is still sliding in', () => {
        const inserting = { phase: 'inserting', diskId: 7 } as const;

        expect(driveReducer(inserting, { type: 'eject' })).toBe(inserting);
    });

    it('ignores settle on an idle drive', () => {
        expect(driveReducer(emptyDrive, { type: 'settle' })).toBe(emptyDrive);
    });
});
