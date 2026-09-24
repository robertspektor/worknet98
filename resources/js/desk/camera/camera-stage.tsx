import type { MouseEvent, ReactNode } from 'react';
import {
    useEffect,
    useLayoutEffect,
    useMemo,
    useReducer,
    useRef,
    useState,
} from 'react';
import { useIsPoweredOn } from '../computer/computer-provider';
import { CameraContext } from './camera-provider';
import type { FocusTarget } from './camera-state';
import { focusedTarget, nextCameraView, ROOM_VIEW } from './camera-state';
import { DetailMenu } from './detail-menu';
import type { Frame } from './framing';
import { boxInStage, frameBox, ROOM_FRAME, SHOTS } from './framing';
import { OverviewButton } from './overview-button';

const CONTROLS = 'button, a, input, select, textarea, label, [role="button"]';

/* What the camera has to do to put one thing in the middle of the window,
   measured through the transform it is applying right now. */

function shotFor(stage: HTMLElement, target: FocusTarget): Frame {
    const shot = SHOTS[target];
    const element = stage.querySelector(shot.selector);

    if (!element) {
        return ROOM_FRAME;
    }

    const stageBox = stage.getBoundingClientRect();
    const viewport = { width: stage.offsetWidth, height: stage.offsetHeight };
    const scale = stageBox.width / viewport.width;

    return frameBox(
        boxInStage(element.getBoundingClientRect(), stageBox, scale),
        viewport,
        shot.fill,
    );
}

function nameOf(element: Element): FocusTarget | null {
    const hit = element.closest<HTMLElement>('[data-focus]');

    return (hit?.dataset.focus as FocusTarget | undefined) ?? null;
}

/* The room is seen in two ways: the whole of it, and one thing in it up
   close. Clicking a thing steps in, clicking past it steps back out, and so
   does Escape and the icon in the corner. */

export function CameraStage({ children }: { children: ReactNode }) {
    const [view, move] = useReducer(nextCameraView, ROOM_VIEW);
    const [frame, setFrame] = useState<Frame>(ROOM_FRAME);
    const stage = useRef<HTMLDivElement>(null);
    const isRunning = useIsPoweredOn();
    const wasRunning = useRef(isRunning);
    const target = focusedTarget(view);

    useLayoutEffect(() => {
        const element = stage.current;

        if (!element) {
            return;
        }

        const apply = () =>
            setFrame(target === null ? ROOM_FRAME : shotFor(element, target));

        apply();
        window.addEventListener('resize', apply);

        return () => window.removeEventListener('resize', apply);
    }, [target]);

    /* Switching the machine on puts the player in front of the screen,
       switching it off hands the room back. */

    useEffect(() => {
        if (wasRunning.current === isRunning) {
            return;
        }

        wasRunning.current = isRunning;
        move(
            isRunning ? { type: 'focus', target: 'monitor' } : { type: 'room' },
        );
    }, [isRunning]);

    useEffect(() => {
        const onKeyDown = (event: KeyboardEvent) => {
            if (event.key === 'Escape' && target !== null) {
                move({ type: 'room' });
            }
        };

        window.addEventListener('keydown', onKeyDown);

        return () => window.removeEventListener('keydown', onKeyDown);
    }, [target]);

    const onClick = (event: MouseEvent<HTMLDivElement>) => {
        if (
            !(event.target instanceof Element) ||
            event.target.closest(CONTROLS)
        ) {
            return;
        }

        /* In front of the screen every click belongs to the machine. */

        if (target === 'monitor') {
            if (event.target.closest('.screen') === null) {
                move({ type: 'room' });
            }

            return;
        }

        const name = nameOf(event.target);

        if (name !== null && name !== target) {
            move({ type: 'focus', target: name });

            return;
        }

        if (name === null && target !== null) {
            move({ type: 'room' });
        }
    };

    const camera = useMemo(
        () => ({
            view,
            focus: (focused: FocusTarget) =>
                move({ type: 'focus', target: focused }),
            toRoom: () => move({ type: 'room' }),
        }),
        [view],
    );

    return (
        <CameraContext value={camera}>
            <div
                className={`camera ${target === null ? 'is-room' : `is-detail is-${target}`}`}
            >
                <div
                    ref={stage}
                    className="stage"
                    style={{
                        transform: `translate(${frame.x}px, ${frame.y}px) scale(${frame.scale})`,
                    }}
                    onClick={onClick}
                >
                    {children}
                </div>
                {target !== null && (
                    <>
                        <DetailMenu target={target} />
                        <OverviewButton onPress={camera.toRoom} />
                    </>
                )}
            </div>
        </CameraContext>
    );
}
