import './bootstrap';

import Alpine from 'alpinejs';

// CSS を Vite 経由でバンドルする
import '../css/app.css';

// 必要なページ固有スクリプトを読み込む（例: welcome）
import './welcome';

window.Alpine = Alpine;

Alpine.start();
