document.addEventListener('DOMContentLoaded', function () {
    const calendarBody = document.getElementById('calendar-body');
    const currentMonthElement = document.getElementById('current-month');
    const prevMonthButton = document.getElementById('prev-month');
    const nextMonthButton = document.getElementById('next-month');
    const morningSlots = document.getElementById('morning-slots');
    const afternoonSlots = document.getElementById('afternoon-slots');
    const eveningSlots = document.getElementById('evening-slots');

    let currentDate = new Date(2025, 2, 1); // Март 2025
    let selectedDay = null; // Выбранный день
    let selectedTime = null; // Выбранное время

    // Функция для генерации времени с 9:00 до 18:00 с интервалом 15 минут
    function generateTimeSlots() {
        morningSlots.innerHTML = '';
        afternoonSlots.innerHTML = '';
        eveningSlots.innerHTML = '';

        for (let hour = 9; hour <= 18; hour++) {
            for (let minute = 0; minute < 60; minute += 15) {
                if (hour === 18 && minute > 0) break; // Останавливаемся на 18:00
                const time = `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
                const timeSlot = document.createElement('div');
                timeSlot.classList.add('time-slot');
                timeSlot.textContent = time;
                timeSlot.addEventListener('click', () => {
                    if (selectedTime) selectedTime.classList.remove('selected');
                    selectedTime = timeSlot;
                    timeSlot.classList.add('selected');
                });

                // Группировка по утру, дню и вечеру
                if (hour < 12) {
                    morningSlots.appendChild(timeSlot);
                } else if (hour < 16) {
                    afternoonSlots.appendChild(timeSlot);
                } else {
                    eveningSlots.appendChild(timeSlot);
                }
            }
        }
    }

    // Функция для отрисовки календаря
    function renderCalendar() {
        calendarBody.innerHTML = '';
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        currentMonthElement.textContent = new Intl.DateTimeFormat('ru-RU', {
            year: 'numeric',
            month: 'long'
        }).format(currentDate);

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDay = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1; // Понедельник как первый день недели

        let date = 1;
        for (let i = 0; i < 6; i++) {
            for (let j = 0; j < 7; j++) {
                const day = document.createElement('div');
                if (i === 0 && j < startDay) {
                    day.classList.add('calendar-day', 'empty');
                } else if (date > lastDay.getDate()) {
                    day.classList.add('calendar-day', 'empty');
                } else {
                    day.classList.add('calendar-day');
                    day.textContent = date;
                    if (date === 10 || date === 11 || date === 12 || date === 13 || date === 14 ||
                        date === 17 || date === 18 || date === 19 || date === 20 || date === 21 ||
                        date === 24 || date === 25 || date === 26 || date === 27 || date === 28) {
                        day.innerHTML = `<strong>${date}</strong>`;
                    }
                    day.addEventListener('click', () => {
                        if (selectedDay) selectedDay.classList.remove('selected');
                        selectedDay = day;
                        day.classList.add('selected');
                    });
                    date++;
                }
                calendarBody.appendChild(day);
            }
        }
    }

    // Переключение месяцев
    prevMonthButton.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    nextMonthButton.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    // Инициализация
    generateTimeSlots();
    renderCalendar();
});