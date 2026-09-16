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
