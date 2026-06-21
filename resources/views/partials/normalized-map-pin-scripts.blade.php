<script>
    function repositionNormalizedMapPins(container, img, pinSelector) {
        if (!container || !img) {
            return { width: 0, height: 0, offsetX: 0, offsetY: 0 };
        }

        const containerRect = container.getBoundingClientRect();
        const imgRect = img.getBoundingClientRect();
        const offsetX = imgRect.left - containerRect.left;
        const offsetY = imgRect.top - containerRect.top;

        if (imgRect.width <= 0 || imgRect.height <= 0) {
            return { width: 0, height: 0, offsetX, offsetY };
        }

        container.querySelectorAll(pinSelector).forEach((pin) => {
            const x = parseFloat(pin.dataset.x || '');
            const y = parseFloat(pin.dataset.y || '');

            if (Number.isNaN(x) || Number.isNaN(y)) {
                pin.style.display = 'none';
                return;
            }

            pin.style.left = `${offsetX + (x * imgRect.width)}px`;
            pin.style.top = `${offsetY + (y * imgRect.height)}px`;
            pin.style.display = '';
        });

        return {
            width: imgRect.width,
            height: imgRect.height,
            offsetX,
            offsetY,
        };
    }
</script>
