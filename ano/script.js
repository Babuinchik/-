document.addEventListener('DOMContentLoaded', function() {
    // Инициализация календаря для бронирования
    const bookingDate = document.getElementById('booking_date');
    if (bookingDate) {
        const today = new Date().toISOString().split('T')[0];
        bookingDate.min = today;
    }
    
    // Валидация форм
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let valid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = 'red';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!valid) {
                e.preventDefault();
                alert('Пожалуйста, заполните все обязательные поля');
            }
        });
    });
});