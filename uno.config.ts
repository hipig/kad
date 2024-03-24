// uno.config.ts
import { defineConfig, presetUno } from 'unocss';
import presetIcons from '@unocss/preset-icons';

export default defineConfig({
    rules: [],
    presets: [
        presetUno(),
        presetIcons(),
    ]
})
