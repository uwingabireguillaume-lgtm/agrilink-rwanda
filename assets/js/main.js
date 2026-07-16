// AgriLink Rwanda — small client-side helpers

document.addEventListener('DOMContentLoaded', function () {
  // Auto-dismiss alerts after 5s
  document.querySelectorAll('.alert').forEach(function (alert) {
    setTimeout(function () {
      const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
      if (bsAlert) bsAlert.close();
    }, 5000);
  });

  // Quantity steppers on product detail / cart pages
  document.querySelectorAll('[data-qty-input]').forEach(function (wrapper) {
    const input = wrapper.querySelector('input[type="number"]');
    const minus = wrapper.querySelector('[data-qty-minus]');
    const plus = wrapper.querySelector('[data-qty-plus]');
    if (!input) return;

    minus && minus.addEventListener('click', function () {
      const val = Math.max(parseInt(input.min || '1', 10), (parseInt(input.value, 10) || 1) - 1);
      input.value = val;
    });
    plus && plus.addEventListener('click', function () {
      const max = input.max ? parseInt(input.max, 10) : Infinity;
      const val = Math.min(max, (parseInt(input.value, 10) || 1) + 1);
      input.value = val;
    });
  });

  // Simple client-side form validation feedback (Bootstrap pattern)
  document.querySelectorAll('form[data-validate]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    });
  });
});
