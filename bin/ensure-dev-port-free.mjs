import { connect } from 'node:net';

const DEV_SERVER_PORT = 5190;
const HOSTS = ['127.0.0.1', '::1'];

function isListening(host) {
    return new Promise((resolve) => {
        const socket = connect({ host, port: DEV_SERVER_PORT });
        socket.once('connect', () => {
            socket.destroy();
            resolve(true);
        });
        socket.once('error', () => resolve(false));
    });
}

const results = await Promise.all(HOSTS.map(isListening));

if (results.includes(true)) {
    console.error(
        `A Vite dev server is already running on port ${DEV_SERVER_PORT}. ` +
            'Starting a second one would delete public/hot and the fonts manifest of the running server.',
    );
    process.exit(1);
}
