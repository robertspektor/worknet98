const TEXT_FILE_NAME = /^[A-Z0-9_-]{1,8}\.TXT$/;

export function toTextFileName(input: string): string | null {
    const upper = input.trim().toUpperCase();
    const name = upper.includes('.') ? upper : `${upper}.TXT`;

    return TEXT_FILE_NAME.test(name) ? name : null;
}
