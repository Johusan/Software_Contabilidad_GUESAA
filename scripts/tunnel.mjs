import { spawn } from 'child_process';
import fs from 'fs';
import https from 'https';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const rootDir = path.resolve(__dirname, '..');
const storageDir = path.join(rootDir, 'storage', 'app');
const statusFile = path.join(storageDir, 'tunnel.json');
const binDir = path.join(rootDir, '.bin');
const exePath = path.join(binDir, 'cloudflared.exe');

// Asegurar directorios
if (!fs.existsSync(storageDir)) {
    fs.mkdirSync(storageDir, { recursive: true });
}

if (!fs.existsSync(binDir)) {
    fs.mkdirSync(binDir, { recursive: true });
}

const writeStatus = (active, url = null) => {
    try {
        fs.writeFileSync(statusFile, JSON.stringify({
            active,
            url,
            updated_at: new Date().toISOString()
        }, null, 2));
    } catch (e) {
        console.error('Error al guardar estado del túnel:', e.message);
    }
};

const downloadBinary = () => {
    return new Promise((resolve, reject) => {
        if (fs.existsSync(exePath) && fs.statSync(exePath).size > 1000000) {
            return resolve(exePath);
        }

        console.log('Descargando cloudflared oficial para Windows (única vez)...');
        const url = 'https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-windows-amd64.exe';

        const file = fs.createWriteStream(exePath);
        const request = (downloadUrl) => {
            https.get(downloadUrl, (response) => {
                if (response.statusCode === 302 || response.statusCode === 301) {
                    return request(response.headers.location);
                }

                response.pipe(file);
                file.on('finish', () => {
                    file.close();
                    console.log('✓ cloudflared descargado con éxito.');
                    resolve(exePath);
                });
            }).on('error', (err) => {
                fs.unlink(exePath, () => { });
                reject(err);
            });
        };
        request(url);
    });
};

const startTunnel = async () => {
    try {
        await downloadBinary();

        console.log('===============================================================');
        console.log('  INICIANDO CLOUDFLARE QUICK TUNNEL');
        console.log('  Redirigiendo a: http://localhost:8000');
        console.log('===============================================================');

        const proc = spawn(exePath, ['tunnel', '--url', 'http://localhost:8000'], {
            stdio: ['ignore', 'pipe', 'pipe']
        });

        let detectedUrl = null;

        const parseOutput = (data) => {
            const str = data.toString();
            const match = str.match(/https:\/\/[a-zA-Z0-9-]+\.trycloudflare\.com/);

            if (match && !detectedUrl) {
                detectedUrl = match[0];
                console.log('\n¡TÚNEL CONECTADO EXITOSAMENTE!');
                console.log(`URL Remota: ${detectedUrl}`);
                console.log('El sistema web ya actualizó el Código QR automáticamente.');
                console.log('Presiona Ctrl + C para detener el túnel cuando desees.\n');
                writeStatus(true, detectedUrl);
            }
        };

        proc.stdout.on('data', parseOutput);
        proc.stderr.on('data', parseOutput);

        proc.on('close', (code) => {
            console.log(`Túnel cerrado (código ${code})`);
            writeStatus(false, null);
        });

        const cleanup = () => {
            writeStatus(false, null);
            proc.kill();
            process.exit();
        };

        process.on('SIGINT', cleanup);
        process.on('SIGTERM', cleanup);
        process.on('exit', () => writeStatus(false, null));

    } catch (err) {
        console.error('Error al iniciar el túnel:', err);
        writeStatus(false, null);
    }
};

startTunnel();
