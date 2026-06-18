<script>
    let animalQrState = { name: '', code: '', scanUrl: '', qrImageUrl: '' };

    function showAnimalQrToast(msg) {
        const toast = document.getElementById('toast');
        if (!toast) return;
        toast.textContent = msg;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2800);
    }

    function resolveAnimalQrUrl({ scanUrl = '', payload = {}, publicUrl = '' } = {}) {
        if (scanUrl) {
            return scanUrl;
        }

        const profileId = payload?.profile_id;
        if (!profileId) {
            return '';
        }

        const base = (publicUrl || window.location.origin).replace(/\/$/, '');
        return `${base}/app/animals/${profileId}`;
    }

    function resolveQrOrigin({ publicUrl = '' } = {}) {
        if (publicUrl) {
            return publicUrl.replace(/\/$/, '');
        }

        return window.location.origin.replace(/\/$/, '');
    }

    function openAnimalQR({ name, subtitle = '', code = '', group = '', image = '', scanUrl = '', publicUrl = '', qrImageUrl = '', payload = {} } = {}) {
        const resolvedScanUrl = resolveAnimalQrUrl({ scanUrl, payload, publicUrl });

        if (!qrImageUrl || !resolvedScanUrl) {
            showAnimalQrToast('تعذّر إنشاء رابط QR لهذا الحيوان');
            return;
        }

        animalQrState = {
            name: name || 'حيوان',
            code: code || '—',
            scanUrl: resolvedScanUrl,
            qrImageUrl,
        };

        const nameEl = document.getElementById('qrAnimalName');
        const metaEl = document.getElementById('qrAnimalMeta');
        const codeEl = document.getElementById('qrAnimalCode');
        const qrImg = document.getElementById('qrImage');
        const loading = document.getElementById('qrLoading');
        const modal = document.getElementById('qrModal');
        const hintTextEl = document.getElementById('qrHintText');
        const scanUrlEl = document.getElementById('qrScanUrl');

        if (!nameEl || !metaEl || !codeEl || !qrImg || !loading || !modal) {
            showAnimalQrToast('تعذّر فتح نافذة QR');
            return;
        }

        if (hintTextEl) {
            hintTextEl.textContent = 'عند المسح تُفتح صفحة تحتوي على معلومات هذا الحيوان فقط.';
        }

        if (scanUrlEl) {
            scanUrlEl.textContent = resolvedScanUrl;
        }

        nameEl.textContent = name || '—';
        metaEl.textContent = subtitle || 'محتوى تعريفي للزوار';
        codeEl.textContent = 'رمز: ' + (code || '—');

        const groupBadge = document.getElementById('qrAnimalGroup');
        if (groupBadge) {
            if (group) {
                groupBadge.textContent = group;
                groupBadge.style.display = 'inline-flex';
            } else {
                groupBadge.style.display = 'none';
            }
        }

        const avatarImg = document.getElementById('qrAvatarImg');
        const avatarEmoji = document.getElementById('qrAvatarEmoji');
        if (avatarImg && avatarEmoji) {
            if (image) {
                avatarImg.src = image;
                avatarImg.alt = name || '';
                avatarImg.style.display = 'block';
                avatarEmoji.style.display = 'none';
                avatarImg.onerror = () => {
                    avatarImg.style.display = 'none';
                    avatarEmoji.style.display = 'block';
                };
            } else {
                avatarImg.style.display = 'none';
                avatarEmoji.style.display = 'block';
            }
        }

        qrImg.style.display = 'none';
        loading.style.display = 'flex';
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        qrImg.onload = () => {
            loading.style.display = 'none';
            qrImg.style.display = 'block';
        };

        qrImg.onerror = () => {
            loading.style.display = 'none';
            qrImg.style.display = 'none';
            showAnimalQrToast('تعذّر توليد رمز QR');
        };

        const qrOrigin = resolveQrOrigin({ publicUrl });
        const imageSource = `${qrImageUrl}?origin=${encodeURIComponent(qrOrigin)}&t=${Date.now()}`;
        qrImg.src = imageSource;
    }

    function closeAnimalQR() {
        const modal = document.getElementById('qrModal');
        if (!modal) return;
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function downloadAnimalQR() {
        const qrImg = document.getElementById('qrImage');
        if (!qrImg || qrImg.style.display === 'none' || !qrImg.src) {
            showAnimalQrToast('انتظر حتى يكتمل توليد الرمز');
            return;
        }

        const link = document.createElement('a');
        link.download = 'QR-' + (animalQrState.name || 'animal') + '.svg';
        link.href = qrImg.src;
        link.click();
        showAnimalQrToast('تم تحميل رمز QR');
    }

    async function copyAnimalScanUrl() {
        if (!animalQrState.scanUrl) {
            showAnimalQrToast('لا يوجد رابط للنسخ');
            return;
        }

        try {
            await navigator.clipboard.writeText(animalQrState.scanUrl);
            showAnimalQrToast('تم نسخ رابط صفحة الحيوان');
        } catch (_) {
            showAnimalQrToast('تعذّر النسخ من المتصفح');
        }
    }

    async function copyAnimalQRCode() {
        if (!animalQrState.code || animalQrState.code === '—') {
            showAnimalQrToast('لا يوجد رمز للنسخ');
            return;
        }

        try {
            await navigator.clipboard.writeText(animalQrState.code);
            showAnimalQrToast('تم نسخ رمز الحيوان');
        } catch (_) {
            showAnimalQrToast('تعذّر النسخ من المتصفح');
        }
    }

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('.js-animal-qr-trigger');
        if (!trigger) return;

        event.preventDefault();

        try {
            const data = JSON.parse(trigger.dataset.qr || '{}');
            openAnimalQR(data);
        } catch (_) {
            showAnimalQrToast('تعذّر قراءة بيانات QR');
        }
    });

    document.getElementById('qrModal')?.addEventListener('click', (event) => {
        if (event.target.id === 'qrModal') closeAnimalQR();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && document.getElementById('qrModal')?.classList.contains('show')) {
            closeAnimalQR();
        }
    });
</script>
