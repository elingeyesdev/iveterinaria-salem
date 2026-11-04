<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@php
    $flashMessages = [];

    foreach (['success', 'error', 'warning', 'info'] as $type) {
        $message = session($type);

        if (!empty($message)) {
            $flashMessages[] = [
                'type' => $type,
                'message' => $message,
            ];
        }
    }
@endphp

@if (!empty($flashMessages))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const messages = @json($flashMessages);

            messages.forEach(function (item) {
                const titleMap = {
                    success: '¡Éxito!',
                    error: '¡Error!',
                    warning: 'Advertencia',
                    info: 'Información',
                };

                Swal.fire({
                    icon: item.type,
                    title: titleMap[item.type] || 'Notificación',
                    text: item.message,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    didOpen: function (toast) {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            });
        });
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('submit', function (event) {
            const form = event.target;

            if (!(form instanceof HTMLFormElement) || !form.classList.contains('form-eliminar')) {
                return;
            }

            if (form.dataset.sweetalertConfirmed === 'true') {
                form.dataset.sweetalertConfirmed = 'false';
                return;
            }

            event.preventDefault();

            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción eliminará el registro de forma permanente. ¡No podrás revertirla!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.dataset.sweetalertConfirmed = 'true';
                    form.submit();
                }
            });
        });
    });
</script>
