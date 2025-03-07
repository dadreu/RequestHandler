document.addEventListener('DOMContentLoaded', function () {
    const calendarBody = document.getElementById('calendar-body');
    const currentMonthElement = document.getElementById('current-month');
    const prevMonthButton = document.getElementById('prev-month');
    const nextMonthButton = document.getElementById('next-month');
    const timeSlotsContainer = document.getElementById('time-slots');
    const confirmButtonContainer = document.getElementById('confirm-button-container');
    const nameInput = document.querySelector('input[type="text"]');
    const phoneInput = document.querySelector('input[type="tel"]');


    
    // Проверка, что все необходимые элементы существуют
    if (!calendarBody || !currentMonthElement || !prevMonthButton || !nextMonthButton || !timeSlotsContainer || !confirmButtonContainer) {
        console.error('Один из элементов не найден на странице.');
        return;
    }

    let currentDate = new Date(2025, 2, 1); // Март 2025
    let selectedDay = null; // Выбранный день
    let selectedTime = null; // Выбранное время

    // Функция для генерации времени с 9:00 до 18:00 с интервалом 15 минут
    function generateTimeSlots() {
        if (!timeSlotsContainer) {
            console.error('Элемент time-slots не найден.');
            return;
        }

        timeSlotsContainer.innerHTML = '';
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
                    showConfirmButton();
                });
                timeSlotsContainer.appendChild(timeSlot);
            }
        }
    }

    // Функция для отрисовки календаря
    function renderCalendar() {
        if (!calendarBody) {
            console.error('Элемент calendar-body не найден.');
            return;
        }

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
                        showConfirmButton();
                    });
                    date++;
                }
                calendarBody.appendChild(day);
            }
        }
    }

    // Функция для отображения кнопки "Записаться"
    function showConfirmButton() {
        if (!confirmButtonContainer) {
            console.error('Элемент confirm-button-container не найден.');
            return;
        }

        if (selectedDay && selectedTime) {
            confirmButtonContainer.innerHTML = `
                <button class="confirm-button" onclick="window.location.href='appointment.html'">Записаться</button>
            `;
        } else {
            confirmButtonContainer.innerHTML = '';
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