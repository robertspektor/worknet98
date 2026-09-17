type Note = [frequency: number, offset: number, duration: number];

class Sound {
    private context: AudioContext | null = null;

    unlock(): void {
        this.context ??= new AudioContext();

        if (this.context.state === 'suspended') {
            void this.context.resume();
        }
    }

    click(): void {
        this.play([[1400, 0, 0.02]], 'square', 0.015);
    }

    error(): void {
        this.play([[160, 0, 0.25]], 'sawtooth', 0.04);
    }

    mail(): void {
        this.play(
            [
                [880, 0, 0.12],
                [1175, 0.12, 0.25],
            ],
            'sine',
            0.06,
        );
    }

    chat(): void {
        this.play(
            [
                [740, 0, 0.06],
                [740, 0.11, 0.06],
            ],
            'triangle',
            0.07,
        );
    }

    floppySeek(): void {
        this.play(
            [
                [95, 0, 0.07],
                [70, 0.09, 0.07],
                [95, 0.18, 0.07],
                [70, 0.27, 0.07],
                [110, 0.42, 0.12],
            ],
            'square',
            0.035,
        );
    }

    floppyEject(): void {
        this.play(
            [
                [320, 0, 0.04],
                [170, 0.05, 0.09],
            ],
            'square',
            0.04,
        );
    }

    hddSeek(): void {
        this.play(
            [
                [60, 0, 0.03],
                [85, 0.08, 0.03],
                [55, 0.13, 0.04],
                [90, 0.26, 0.03],
                [65, 0.34, 0.05],
            ],
            'square',
            0.02,
        );
    }

    startup(): void {
        this.play(
            [
                [523, 0, 0.3],
                [659, 0.15, 0.3],
                [784, 0.3, 0.3],
                [1047, 0.45, 0.8],
            ],
            'triangle',
            0.08,
        );
    }

    screw(): void {
        this.play(
            [
                [1900, 0, 0.02],
                [1700, 0.05, 0.02],
                [2100, 0.1, 0.02],
            ],
            'square',
            0.012,
        );
    }

    latch(): void {
        this.play(
            [
                [900, 0, 0.03],
                [420, 0.04, 0.05],
            ],
            'square',
            0.03,
        );
    }

    zap(): void {
        this.play(
            [
                [2400, 0, 0.05],
                [180, 0.05, 0.12],
                [2200, 0.12, 0.04],
            ],
            'sawtooth',
            0.05,
        );
    }

    fanSpinUp(): void {
        this.play(
            [
                [80, 0, 0.3],
                [120, 0.2, 0.3],
                [170, 0.4, 0.5],
            ],
            'triangle',
            0.05,
        );
    }

    private play(notes: Note[], type: OscillatorType, volume: number): void {
        const context = this.context;

        if (!context) {
            return;
        }

        notes.forEach(([frequency, offset, duration]) => {
            const startAt = context.currentTime + offset;
            const oscillator = context.createOscillator();
            const gain = context.createGain();

            oscillator.type = type;
            oscillator.frequency.value = frequency;
            gain.gain.setValueAtTime(volume, startAt);
            gain.gain.exponentialRampToValueAtTime(0.0001, startAt + duration);
            oscillator.connect(gain).connect(context.destination);
            oscillator.start(startAt);
            oscillator.stop(startAt + duration);
        });
    }
}

export const sound = new Sound();
