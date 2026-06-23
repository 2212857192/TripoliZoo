<script>
    /**
     * Returns the actual rendered image content rect (width, height, offsetX, offsetY)
     * relative to the img element's top-left corner, accounting for object-fit: contain
     * letterboxing. Without this correction, pins are placed on the element's full bounding
     * box instead of the visible image pixels, causing coordinates to be wrong whenever
     * the image doesn't fill its element completely (e.g. wide container, tall image).
     */
    function getImageContentRect(img) {
        const elemW = img.clientWidth;
        const elemH = img.clientHeight;

        if (!img.naturalWidth || !img.naturalHeight || elemW <= 0 || elemH <= 0) {
            return { offsetX: 0, offsetY: 0, width: elemW, height: elemH };
        }

        const naturalAspect = img.naturalWidth / img.naturalHeight;
        const elemAspect = elemW / elemH;

        let contentW, contentH, contentOffsetX, contentOffsetY;

        if (naturalAspect > elemAspect) {
            // Image is wider → constrained by element width, letterbox top/bottom
            contentW = elemW;
            contentH = elemW / naturalAspect;
            contentOffsetX = 0;
            contentOffsetY = (elemH - contentH) / 2;
        } else {
            // Image is taller (or equal) → constrained by element height, letterbox left/right
            contentH = elemH;
            contentW = elemH * naturalAspect;
            contentOffsetX = (elemW - contentW) / 2;
            contentOffsetY = 0;
        }

        return { offsetX: contentOffsetX, offsetY: contentOffsetY, width: contentW, height: contentH };
    }

    function repositionNormalizedMapPins(container, img, pinSelector) {
        if (!container || !img) {
            return { width: 0, height: 0, offsetX: 0, offsetY: 0 };
        }

        const containerRect = container.getBoundingClientRect();
        const imgElemRect = img.getBoundingClientRect();
        const content = getImageContentRect(img);

        if (content.width <= 0 || content.height <= 0) {
            return { width: 0, height: 0, offsetX: 0, offsetY: 0 };
        }

        // Total offset = (img element relative to container) + (content within img element)
        const baseX = (imgElemRect.left - containerRect.left) + content.offsetX;
        const baseY = (imgElemRect.top - containerRect.top) + content.offsetY;

        container.querySelectorAll(pinSelector).forEach((pin) => {
            const x = parseFloat(pin.dataset.x || '');
            const y = parseFloat(pin.dataset.y || '');

            if (Number.isNaN(x) || Number.isNaN(y)) {
                pin.style.display = 'none';
                return;
            }

            pin.style.left = `${baseX + (x * content.width)}px`;
            pin.style.top = `${baseY + (y * content.height)}px`;
            pin.style.display = '';
        });

        return { width: content.width, height: content.height, offsetX: baseX, offsetY: baseY };
    }
</script>
