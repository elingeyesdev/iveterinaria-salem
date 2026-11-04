const form = document.getElementById('pagos-search-form');

if (form) {
    const table = document.getElementById('table-container');
    const error = document.getElementById('pagos-search-error');
    let controller;

    async function actualizarListado(page = '1') {
        controller?.abort();
        const current = new AbortController();
        controller = current;
        const url = new URL(form.action);
        url.search = new URLSearchParams(new FormData(form)).toString();
        url.searchParams.set('page', page);
        table.setAttribute('aria-busy', 'true');
        error.hidden = true;

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                signal: current.signal,
            });
            if (!response.ok || response.redirected) {
                throw new Error('No se pudo actualizar el listado. Revisa el estado seleccionado o recarga la página.');
            }
            const html = await response.text();
            if (controller !== current) return;
            table.innerHTML = html;
            history.replaceState(null, '', url);
        } catch (exception) {
            if (exception.name !== 'AbortError' && controller === current) {
                error.textContent = exception.message;
                error.hidden = false;
            }
        } finally {
            if (controller === current) table.removeAttribute('aria-busy');
        }
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        actualizarListado();
    });
    form.elements.estado.addEventListener('change', () => actualizarListado());
    form.elements.perPage.addEventListener('change', () => actualizarListado());
    table.addEventListener('submit', (event) => {
        if (event.target.matches('.form-anular-pago') && !window.confirm('¿Anular este pago? El movimiento se conservará en el historial y dejará de contar como cobrado.')) {
            event.preventDefault();
        }
    });
    table.addEventListener('click', (event) => {
        const link = event.target.closest('a.page-link');
        if (!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        actualizarListado(new URL(link.href).searchParams.get('page') || '1');
    });
}
