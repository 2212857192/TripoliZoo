@include('partials.normalized-map-pin-scripts')
<script>
    function initAdminMapPicker(pickerId, latInputId, lngInputId, pinId, initialLng, initialLat) {
        const picker = document.getElementById(pickerId);
        const pin = document.getElementById(pinId);
        const latInput = document.getElementById(latInputId);
        const lngInput = document.getElementById(lngInputId);
        const img = picker?.querySelector('img');

        if (!picker || !pin || !latInput || !lngInput || !img) return;

        function imageMetrics() {
            const pickerRect = picker.getBoundingClientRect();
            const imgRect = img.getBoundingClientRect();

            return {
                offsetX: imgRect.left - pickerRect.left,
                offsetY: imgRect.top - pickerRect.top,
                width: imgRect.width,
                height: imgRect.height,
            };
        }

        function isNormalized(x, y) {
            return x >= 0 && x <= 1 && y >= 0 && y <= 1;
        }

        function setPin(x, y) {
            const safeX = Math.max(0, Math.min(1, x));
            const safeY = Math.max(0, Math.min(1, y));

            lngInput.value = safeX.toFixed(7);
            latInput.value = safeY.toFixed(7);

            const metrics = imageMetrics();
            if (metrics.width <= 0 || metrics.height <= 0) return;

            pin.style.left = `${metrics.offsetX + (safeX * metrics.width)}px`;
            pin.style.top = `${metrics.offsetY + (safeY * metrics.height)}px`;
            pin.style.display = 'block';
        }

        function refreshPinFromInputs() {
            const x = parseFloat(lngInput.value);
            const y = parseFloat(latInput.value);

            if (Number.isNaN(x) || Number.isNaN(y) || !isNormalized(x, y)) {
                pin.style.display = 'none';
                return;
            }

            setPin(x, y);
        }

        function placeFromEvent(event) {
            const metrics = imageMetrics();
            const localX = event.clientX - picker.getBoundingClientRect().left - metrics.offsetX;
            const localY = event.clientY - picker.getBoundingClientRect().top - metrics.offsetY;

            if (metrics.width <= 0 || metrics.height <= 0) return;

            const x = localX / metrics.width;
            const y = localY / metrics.height;

            if (x < 0 || x > 1 || y < 0 || y > 1) return;

            setPin(x, y);
        }

        picker.addEventListener('click', placeFromEvent);
        window.addEventListener('resize', refreshPinFromInputs);

        function boot() {
            if (initialLng !== null && initialLat !== null && !Number.isNaN(initialLng) && !Number.isNaN(initialLat)) {
                if (isNormalized(initialLng, initialLat)) {
                    setPin(initialLng, initialLat);
                } else {
                    latInput.value = '';
                    lngInput.value = '';
                    pin.style.display = 'none';
                }
            } else if (latInput.value && lngInput.value) {
                refreshPinFromInputs();
            }
        }

        if (img.complete) {
            boot();
        } else {
            img.addEventListener('load', boot, { once: true });
        }
    }

    function initAdminMapDisplay(mapBodyId, imageSelector) {
        const mapBody = document.getElementById(mapBodyId);
        const img = mapBody?.querySelector(imageSelector || '.map-image');
        if (!mapBody || !img) return;

        function repositionPins() {
            repositionNormalizedMapPins(mapBody, img, '.map-pin[data-x]');
        }

        window.addEventListener('resize', repositionPins);
        if (img.complete) {
            repositionPins();
        } else {
            img.addEventListener('load', repositionPins, { once: true });
        }
    }
</script>
