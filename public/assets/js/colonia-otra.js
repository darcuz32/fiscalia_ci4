/* Mantiene el catálogo disponible al capturar una colonia no incluida en SEPOMEX. */
(function () {
    var OTHER = '__OTRA__';

    function isColonia(select) {
        return select && select.tagName === 'SELECT' && /colonia/i.test(select.name || select.id || select.className || '');
    }
    function otherName(select) {
        return select.getAttribute('data-colonia-name') || select.getAttribute('name') || '';
    }
    function ensureOtherOption(select) {
        if (!isColonia(select) || select.querySelector('option[value="' + OTHER + '"]')) return;
        var option = document.createElement('option');
        option.value = OTHER;
        option.textContent = 'Otra / Especifique…';
        select.appendChild(option);
        if (window.jQuery && window.jQuery(select).data('chosen')) window.jQuery(select).trigger('chosen:updated');
    }
    function alternateInput(select) {
        var input = select.parentNode && select.parentNode.querySelector('.js-colonia-otra[data-for="' + (select.id || '') + '"]');
        if (input) return input;
        input = document.createElement('input');
        input.type = 'text'; input.className = 'form-control mt-2 js-colonia-otra';
        input.maxLength = select.maxLength > 0 ? select.maxLength : 120;
        input.placeholder = 'Especifique la colonia'; input.disabled = true; input.hidden = true;
        input.setAttribute('data-for', select.id || '');
        input.addEventListener('input', function () { this.value = this.value.replace(/[\x00-\x1F\x7F]/g, ''); });
        select.insertAdjacentElement('afterend', input);
        return input;
    }
    function sync(select, focus) {
        if (!isColonia(select)) return;
        ensureOtherOption(select);
        var name = otherName(select); if (!name) return;
        var input = alternateInput(select);
        if (select.value === OTHER) {
            select.setAttribute('data-colonia-name', name); select.removeAttribute('name');
            input.name = name; input.disabled = false; input.hidden = false; input.required = select.required;
            input.setAttribute('aria-label', 'Especifique la colonia');
            if (focus) input.focus();
        } else {
            select.name = name; input.removeAttribute('name'); input.disabled = true; input.required = false; input.hidden = true;
        }
    }
    function scan(root) {
        if (root && root.nodeType === 1 && isColonia(root)) sync(root, false);
        if ((root || document).querySelectorAll) (root || document).querySelectorAll('select').forEach(function (select) { sync(select, false); });
    }
    document.addEventListener('change', function (event) {
        var select = event.target; if (!isColonia(select)) return;
        if (select.value === OTHER) { event.stopImmediatePropagation(); sync(select, true); return; }
        sync(select, false);
    }, true);
    if (window.jQuery) {
        window.jQuery(document).on('change.coloniaOtra', 'select', function (event) {
            if (!isColonia(this)) return;
            if (this.value === OTHER) { event.stopImmediatePropagation(); sync(this, true); return false; }
            sync(this, false);
        });
    }
    document.addEventListener('DOMContentLoaded', function () {
        scan(document);
        new MutationObserver(function (changes) {
            changes.forEach(function (change) {
                change.addedNodes.forEach(function (node) { if (node.nodeType === 1) scan(node); });
                if (change.target && change.target.nodeType === 1) scan(change.target);
            });
        }).observe(document.body, { childList: true, subtree: true });
    });
})();
