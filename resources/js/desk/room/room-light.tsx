/* The light of the room, laid over everything in it: the warm pool of the
   work lamp, the fall-off into the corners and the grain of the picture.
   The layers blend with the room, so they hang in it instead of sitting in
   a box of their own. With the machine running the camera is at the screen
   and the light steps out of the way. */

export function RoomLight() {
    return (
        <>
            <span className="room-light is-warm" aria-hidden="true" />
            <span className="room-light is-falloff" aria-hidden="true" />
            <span className="room-light is-grain" aria-hidden="true" />
        </>
    );
}
